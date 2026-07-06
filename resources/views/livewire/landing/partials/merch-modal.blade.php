@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $formatPrice = fn ($product) => $product->currency.' '.number_format($product->price, 0, ',', '.');
    $modalProducts = $merchandiseProducts->mapWithKeys(function ($product) use ($imageUrl, $formatPrice) {
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
                'kicker' => $product->kicker ?: 'Official Merch',
                'title' => $product->name,
                'price' => $formatPrice($product),
                'description' => $product->description ?: 'Merchandise resmi Purnama Bersantai.',
                'features' => $product->features->pluck('text')->values(),
                'orderUrl' => $product->order_url ?: route('landing.contact'),
                'gallery' => $gallery,
            ],
        ];
    });
@endphp

<script type="application/json" id="merchandise-products-json">@json($modalProducts)</script>

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
                    class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-200/80"
                >
                    Exclusive Drop
                </p>
                <h3
                    id="merch-modal-title"
                    class="mt-3 font-display text-5xl uppercase tracking-[0.08em] sm:text-6xl"
                >
                    Merchandise
                </h3>
                <p
                    id="merch-modal-price"
                    class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-amber-300"
                >
                    Rp0
                </p>
                <p
                    id="merch-modal-description"
                    class="mt-5 text-base leading-relaxed text-white/72"
                >
                    Detail produk akan tampil di sini.
                </p>
                <ul
                    id="merch-modal-features"
                    class="merch-modal-features mt-6 space-y-3 text-sm font-medium text-white/72 sm:text-base"
                ></ul>
                <a
                    id="merch-modal-order"
                    href="{{ route('landing.contact') }}"
                    class="mt-8 inline-flex w-full items-center justify-center rounded-2xl bg-ember px-6 py-4 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-red-500 sm:w-auto sm:min-w-[12rem]"
                    wire:navigate
                >
                    Order
                </a>
            </div>
        </div>
    </div>
</div>
