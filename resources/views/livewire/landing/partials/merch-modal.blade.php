@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $formatPrice = fn ($product, ?int $price = null) => $product->currency.' '.number_format($price ?? $product->price, 0, ',', '.');
    $safeDescription = function (?string $description, string $fallback): string {
        $content = trim((string) $description) ?: $fallback;
        $content = strip_tags($content, '<p><br><strong><b><em><i><u><ul><ol><li><a><h2><h3><blockquote>');
        $content = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/i', '', $content) ?? $content;

        return preg_replace('/javascript\s*:/i', '', $content) ?? $content;
    };
    $whatsappChannel = collect($contactChannels ?? [])
        ->first(fn ($channel) => $channel->type === 'whatsapp' && (filled($channel->url) || filled($channel->value)));
    $whatsappOrderUrl = null;

    if ($whatsappChannel?->url) {
        $whatsappOrderUrl = $whatsappChannel->url;
    } elseif ($whatsappChannel?->value) {
        $phoneNumber = preg_replace('/\D+/', '', $whatsappChannel->value);
        $phoneNumber = str_starts_with((string) $phoneNumber, '0') ? '62'.substr((string) $phoneNumber, 1) : $phoneNumber;
        $whatsappOrderUrl = $phoneNumber ? 'https://wa.me/'.$phoneNumber : null;
    }

    $modalProducts = $merchandiseProducts->mapWithKeys(function ($product) use ($imageUrl, $formatPrice, $safeDescription, $whatsappOrderUrl) {
        $gallery = $product->images->map(fn ($image) => [
            'src' => $imageUrl($image->image_path, asset('landing/assets/Rectangle 17.png')),
            'alt' => $image->alt_text ?: $product->name,
            'className' => $image->image_class ?: '',
        ])->values();

        if ($gallery->isEmpty()) {
            $gallery = collect([[
                'src' => $imageUrl($product->thumbnail_path, asset('landing/assets/Rectangle 17.png')),
                'alt' => $product->thumbnail_alt ?: $product->name,
                'className' => $product->thumbnail_class ?: '',
            ]]);
        }

        return [
            $product->slug => [
                'kicker' => $product->category?->name ?: ($product->kicker ?: 'Official Merch'),
                'slug' => $product->slug,
                'url' => route('landing.merch.show', ['productSlug' => $product->slug]),
                'title' => $product->name,
                'price' => $formatPrice($product, $product->effectivePrice()),
                'originalPrice' => $formatPrice($product, (int) $product->price),
                'discountPrice' => $product->hasDiscount() ? $formatPrice($product, (int) $product->discount_price) : null,
                'discountPercent' => $product->discountPercent(),
                'hasDiscount' => $product->hasDiscount(),
                'description' => $safeDescription($product->description, 'Merchandise resmi Purnama Bersantai.'),
                'stockQuantity' => (int) ($product->stock_quantity ?? 0),
                'sizes' => collect($product->size_options ?? [])->filter()->values(),
                'colors' => collect($product->color_options ?? [])->filter()->values(),
                'orderUrl' => $whatsappOrderUrl ?: route('landing.contact'),
                'gallery' => $gallery,
            ],
        ];
    });
@endphp

<script
    type="application/json"
    id="merchandise-products-json"
    data-base-url="{{ route('landing.merch') }}"
    data-initial-slug="{{ $initialProductSlug ?? '' }}"
>@json($modalProducts)</script>

<div id="merch-modal" class="merch-modal" aria-hidden="true">
    <div class="merch-modal-backdrop" data-merch-close></div>
    <div
        class="merch-modal-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="merch-modal-title"
    >
        <button
            type="button"
            class="section-slider-button merch-modal-close"
            aria-label="Close merchandise modal"
            data-merch-close
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path
                    d="M6 6L18 18M18 6L6 18"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </button>

        <div class="merch-modal-grid">
            <div class="merch-modal-gallery-shell">
                <div class="merch-modal-main">
                    <img id="merch-modal-image" src="" alt="" />
                </div>
                <div id="merch-modal-gallery" class="merch-modal-thumbs"></div>
            </div>

            <div class="merch-modal-copy text-white">
                <p
                    id="merch-modal-kicker"
                    class="text-sm font-semibold uppercase tracking-[0.28em] text-[#fff700]/80"
                >
                    Exclusive Drop
                </p>
                <h3
                    id="merch-modal-title"
                    class="mt-3 font-display text-5xl uppercase tracking-[0.08em] sm:text-6xl"
                >
                    Merchandise
                </h3>
                <div
                    id="merch-modal-price"
                    class="merch-modal-price-stack mt-4"
                >
                    <p class="merch-modal-price-current">Rp0</p>
                </div>
                <div
                    id="merch-modal-description"
                    class="merch-modal-description mt-5 text-base leading-relaxed text-white/72"
                >
                    Detail produk akan tampil di sini.
                </div>

                <div class="merch-order-form mt-6 space-y-4">
                    <p id="merch-modal-stock" class="text-sm font-semibold text-white/65"></p>

                    <div id="merch-modal-size-field" class="merch-order-field">
                        <label for="merch-modal-size">Size</label>
                        <select id="merch-modal-size"></select>
                    </div>

                    <div id="merch-modal-color-field" class="merch-order-field">
                        <label for="merch-modal-color">Warna</label>
                        <select id="merch-modal-color"></select>
                    </div>

                    <div class="merch-order-field">
                        <label for="merch-modal-qty">Qty</label>
                        <input id="merch-modal-qty" type="number" min="1" value="1" inputmode="numeric" />
                    </div>
                </div>

                <a
                    id="merch-modal-order"
                    href="{{ route('landing.contact') }}"
                    class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-ember px-6 py-4 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-[#fff700] hover:text-[#2f2e2e] sm:w-auto sm:min-w-[12rem]"
                >
                    Order
                </a>
            </div>
        </div>
    </div>
</div>
