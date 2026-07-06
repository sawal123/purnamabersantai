@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $formatPrice = fn ($product) => $product->currency.' '.number_format($product->price, 0, ',', '.');
@endphp

<section id="merch" class="relative z-10 pb-24 pt-32">
    <div class="mx-auto max-w-7xl px-5 lg:px-8">
        <div class="reveal text-center">
            <h2 class="font-display text-5xl uppercase tracking-[0.12em] text-white sm:text-6xl">
                Merchandise Drop
            </h2>
            <p class="mx-auto mt-2 max-w-2xl text-lg text-white/70">
                Pilih koleksi favoritmu lalu buka detail produknya untuk lihat gallery, deskripsi, dan langsung order.
            </p>
        </div>

        @if (($staticLayout ?? false) === true)
            <div class="mt-12 flex flex-wrap justify-center gap-y-6">
                @forelse ($merchandiseProducts as $product)
                    <div class="w-full px-2 sm:w-1/2 lg:w-1/3 xl:w-1/4">
                        <article class="h-full">
                            <button type="button" class="merch-card-button" data-merch-trigger="{{ $product->slug }}">
                                <div class="merch-card-media">
                                    <span class="merch-card-tag">{{ $product->kicker ?: 'Official Merch' }}</span>
                                    <img
                                        src="{{ $imageUrl($product->thumbnail_path, asset('landing/assets/Rectangle 17.png')) }}"
                                        alt="{{ $product->thumbnail_alt ?: $product->name }}"
                                        class="{{ $product->thumbnail_class ?: '' }}"
                                    />
                                </div>
                                <div class="merch-card-copy">
                                    <div>
                                        <p class="font-display text-[2rem] uppercase tracking-[0.08em] text-white">
                                            {{ $product->name }}
                                        </p>
                                    </div>
                                    <div class="merch-card-meta">
                                        <span class="merch-card-price">{{ $formatPrice($product) }}</span>
                                        <span class="merch-card-cta">Detail</span>
                                    </div>
                                </div>
                            </button>
                        </article>
                    </div>
                @empty
                    <div class="w-full max-w-md px-2">
                        <article class="h-full">
                            <a href="{{ route('landing.contact') }}" class="merch-card-button" wire:navigate>
                                <div class="merch-card-media">
                                    <span class="merch-card-tag">Coming Soon</span>
                                    <img src="{{ asset('landing/assets/Rectangle 17.png') }}" alt="Merchandise coming soon" />
                                </div>
                                <div class="merch-card-copy">
                                    <p class="font-display text-[2rem] uppercase tracking-[0.08em] text-white">Merch Coming Soon</p>
                                    <div class="merch-card-meta">
                                        <span class="merch-card-price">IDR 0</span>
                                        <span class="merch-card-cta">Contact</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    </div>
                @endforelse
            </div>
        @else
            <div class="swiper content-swiper merch-swiper reveal mt-12" style="--delay: 80ms">
                <div class="swiper-wrapper">
                    @forelse ($merchandiseProducts as $product)
                        <div class="swiper-slide">
                            <article class="h-full">
                                <button type="button" class="merch-card-button" data-merch-trigger="{{ $product->slug }}">
                                    <div class="merch-card-media">
                                        <span class="merch-card-tag">{{ $product->kicker ?: 'Official Merch' }}</span>
                                        <img
                                            src="{{ $imageUrl($product->thumbnail_path, asset('landing/assets/Rectangle 17.png')) }}"
                                            alt="{{ $product->thumbnail_alt ?: $product->name }}"
                                            class="{{ $product->thumbnail_class ?: '' }}"
                                        />
                                    </div>
                                    <div class="merch-card-copy">
                                        <div>
                                            <p class="font-display text-[2rem] uppercase tracking-[0.08em] text-white">
                                                {{ $product->name }}
                                            </p>
                                        </div>
                                        <div class="merch-card-meta">
                                            <span class="merch-card-price">{{ $formatPrice($product) }}</span>
                                            <span class="merch-card-cta">Detail</span>
                                        </div>
                                    </div>
                                </button>
                            </article>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <article class="h-full">
                                <a href="{{ route('landing.contact') }}" class="merch-card-button" wire:navigate>
                                    <div class="merch-card-media">
                                        <span class="merch-card-tag">Coming Soon</span>
                                        <img src="{{ asset('landing/assets/Rectangle 17.png') }}" alt="Merchandise coming soon" />
                                    </div>
                                    <div class="merch-card-copy">
                                        <p class="font-display text-[2rem] uppercase tracking-[0.08em] text-white">Merch Coming Soon</p>
                                        <div class="merch-card-meta">
                                            <span class="merch-card-price">IDR 0</span>
                                            <span class="merch-card-cta">Contact</span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        </div>
                    @endforelse
                </div>

                <div class="section-slider-bar">
                    <div class="section-slider-pagination merch-pagination"></div>
                    <div class="section-slider-nav">
                        <button type="button" class="section-slider-button merch-prev" aria-label="Previous merchandise slide">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="section-slider-button merch-next" aria-label="Next merchandise slide">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
