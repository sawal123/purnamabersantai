@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $heading = ($landingSectionHeadings ?? collect())->get('gallery');
@endphp

<div
    x-data="{ selectedMoment: null }"
    x-effect="document.body.classList.toggle('modal-open', selectedMoment !== null)"
    x-on:keydown.escape.window="selectedMoment = null"
>
<section id="gallery" class="relative z-10 pb-24 pt-32">
    @include('livewire.landing.partials.section-elements', ['offset' => 3])

    <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
        <div class="reveal text-center">
            @if (filled($heading?->kicker))
                <p class="landing-heading-kicker mb-3">
                    {{ $heading->kicker }}
                </p>
            @endif
            <h2 class="font-display text-5xl uppercase tracking-[0.12em] sm:text-6xl">
                @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Beautiful Moments'])
            </h2>
            <p class="mt-2 text-lg text-white/70">
                {{ $heading?->subtitle ?: 'Relive the night, one moment at a time.' }}
            </p>
        </div>

        @if (($staticLayout ?? false) === true)
            <div class="mt-12 flex flex-wrap justify-center gap-y-6">
                @forelse ($galleryMoments as $moment)
                    @php
                        $momentImage = $imageUrl($moment->image_path, asset('landing/assets/Rectangle 17.png'));
                        $momentAlt = $moment->alt_text ?: $moment->title;
                        $momentUsername = ltrim($moment->username ?: 'purnamabersantai', '@');
                        $momentDescription = $moment->description
                            ?: ($moment->alt_text ?: 'Dokumentasi momen Purnama Bersantai yang menangkap suasana festival, energi penonton, dan cerita hangat dari malam acara.');
                        $momentModalDescription = trim(strip_tags($momentDescription));
                        $momentCardDescription = \Illuminate\Support\Str::limit($momentModalDescription, 74, '....');
                    @endphp
                    <div class="w-full px-2 sm:w-1/2 lg:w-1/3 xl:w-1/4">
                        <article class="overflow-hidden rounded-[1.5rem] bg-white text-zinc-900 transition hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.35)]">
                            <button
                                type="button"
                                class="gallery-card-image-button"
                                aria-label="Open {{ $moment->title }} detail"
                                x-on:click="selectedMoment = {
                                    src: @js($momentImage),
                                    alt: @js($momentAlt),
                                    title: @js($moment->title),
                                    username: @js($momentUsername),
                                    description: @js($momentModalDescription)
                                }"
                            >
                                <img
                                    src="{{ $momentImage }}"
                                    alt="{{ $momentAlt }}"
                                    class="h-[22rem] w-full object-cover"
                                />
                                <span class="gallery-card-image-overlay">View Moment</span>
                            </button>
                            <div class="space-y-4 p-4">
                                <p class="gallery-card-description">{{ $momentCardDescription }}</p>
                                <div class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-zinc-400 text-xs">@</span>
                                    <span>{{ $momentUsername }}</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    @php
                        $fallbackImage = asset('landing/assets/Rectangle 17.png');
                        $fallbackDescription = 'Dokumentasi momen Purnama Bersantai akan tampil di sini setelah gallery ditambahkan dari dashboard.';
                    @endphp
                    <div class="w-full max-w-md px-2">
                        <article class="overflow-hidden rounded-[1.5rem] bg-white text-zinc-900 transition hover:shadow-[0_20px_40px_rgba(0,0,0,0.35)]">
                            <button
                                type="button"
                                class="gallery-card-image-button"
                                aria-label="Open moment detail"
                                x-on:click="selectedMoment = {
                                    src: @js($fallbackImage),
                                    alt: 'Festival memory',
                                    title: 'Moment Coming Soon',
                                    username: 'purnamabersantai',
                                    description: @js($fallbackDescription)
                                }"
                            >
                                <img src="{{ $fallbackImage }}" alt="Festival memory" class="h-[22rem] w-full object-cover" />
                                <span class="gallery-card-image-overlay">View Moment</span>
                            </button>
                            <div class="space-y-4 p-4">
                                <p class="gallery-card-description">{{ \Illuminate\Support\Str::limit($fallbackDescription, 92, '....') }}</p>
                                <div class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                                    <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-zinc-400 text-xs">@</span>
                                    <span>purnamabersantai</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @endforelse
            </div>

            @if ($hasMoreGalleryMoments ?? false)
                <div class="mt-10 flex justify-center">
                    <button
                        type="button"
                        class="inline-flex min-w-[12rem] items-center justify-center gap-3 rounded-2xl border border-white/12 bg-[#2f2e2e] px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] text-white shadow-[0_14px_32px_rgba(0,0,0,0.25)] transition hover:-translate-y-1 hover:bg-[#242323] disabled:cursor-not-allowed disabled:opacity-60"
                        wire:click="loadMore"
                        wire:loading.attr="disabled"
                        wire:target="loadMore"
                    >
                        <span wire:loading.remove wire:target="loadMore">More</span>
                        <span wire:loading wire:target="loadMore" class="inline-flex items-center gap-3">
                            <span class="lineup-spinner h-5 w-5 rounded-full border-2 border-white/20 border-t-[#fff700]"></span>
                            Loading
                        </span>
                    </button>
                </div>
            @endif
        @else
            <div class="swiper content-swiper moments-swiper reveal mt-12" style="--delay: 80ms">
                <div class="swiper-wrapper">
                    @forelse ($galleryMoments as $moment)
                        @php
                            $momentImage = $imageUrl($moment->image_path, asset('landing/assets/Rectangle 17.png'));
                            $momentAlt = $moment->alt_text ?: $moment->title;
                            $momentUsername = ltrim($moment->username ?: 'purnamabersantai', '@');
                            $momentDescription = $moment->description
                                ?: ($moment->alt_text ?: 'Dokumentasi momen Purnama Bersantai yang menangkap suasana festival, energi penonton, dan cerita hangat dari malam acara.');
                            $momentModalDescription = trim(strip_tags($momentDescription));
                            $momentCardDescription = \Illuminate\Support\Str::limit($momentModalDescription, 74, '....');
                        @endphp
                        <div class="swiper-slide">
                            <article class="overflow-hidden rounded-[1.5rem] bg-white text-zinc-900 transition hover:-translate-y-2 hover:shadow-[0_20px_40px_rgba(0,0,0,0.35)]">
                                <button
                                    type="button"
                                    class="gallery-card-image-button"
                                    aria-label="Open {{ $moment->title }} detail"
                                    x-on:click="selectedMoment = {
                                        src: @js($momentImage),
                                        alt: @js($momentAlt),
                                        title: @js($moment->title),
                                        username: @js($momentUsername),
                                        description: @js($momentModalDescription)
                                    }"
                                >
                                    <img
                                        src="{{ $momentImage }}"
                                        alt="{{ $momentAlt }}"
                                        class="h-[22rem] w-full object-cover"
                                    />
                                    <span class="gallery-card-image-overlay">View Moment</span>
                                </button>
                                <div class="space-y-4 p-4">
                                    <p class="gallery-card-description">{{ $momentCardDescription }}</p>
                                    <div class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-zinc-400 text-xs">@</span>
                                        <span>{{ $momentUsername }}</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @empty
                        @php
                            $fallbackImage = asset('landing/assets/Rectangle 17.png');
                            $fallbackDescription = 'Dokumentasi momen Purnama Bersantai akan tampil di sini setelah gallery ditambahkan dari dashboard.';
                        @endphp
                        <div class="swiper-slide">
                            <article class="overflow-hidden rounded-[1.5rem] bg-white text-zinc-900 transition hover:shadow-[0_20px_40px_rgba(0,0,0,0.35)]">
                                <button
                                    type="button"
                                    class="gallery-card-image-button"
                                    aria-label="Open moment detail"
                                    x-on:click="selectedMoment = {
                                        src: @js($fallbackImage),
                                        alt: 'Festival memory',
                                        title: 'Moment Coming Soon',
                                        username: 'purnamabersantai',
                                        description: @js($fallbackDescription)
                                    }"
                                >
                                    <img src="{{ $fallbackImage }}" alt="Festival memory" class="h-[22rem] w-full object-cover" />
                                    <span class="gallery-card-image-overlay">View Moment</span>
                                </button>
                                <div class="space-y-4 p-4">
                                    <p class="gallery-card-description">{{ \Illuminate\Support\Str::limit($fallbackDescription, 92, '....') }}</p>
                                    <div class="flex items-center gap-2 text-sm font-semibold text-zinc-700">
                                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full border border-zinc-400 text-xs">@</span>
                                        <span>purnamabersantai</span>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforelse
                </div>
                <div class="section-slider-bar">
                    <div class="section-slider-pagination moments-pagination"></div>
                    <div class="section-slider-nav">
                        <button type="button" class="section-slider-button moments-prev" aria-label="Previous moments slide">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="section-slider-button moments-next" aria-label="Next moments slide">
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

    <div
        x-cloak
        x-show="selectedMoment"
        x-transition.opacity.duration.200ms
        class="gallery-detail-modal"
        aria-modal="true"
        role="dialog"
        aria-labelledby="gallery-detail-title"
    >
        <button
            type="button"
            class="gallery-detail-backdrop"
            aria-label="Close gallery detail"
            x-on:click="selectedMoment = null"
        ></button>

        <div
            x-show="selectedMoment"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-180"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 scale-95"
            class="gallery-detail-panel"
        >
            <button
                type="button"
                class="gallery-detail-close"
                aria-label="Close gallery detail"
                x-on:click="selectedMoment = null"
            >
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="gallery-detail-grid">
                <div class="gallery-detail-image-shell">
                    <img
                        x-bind:src="selectedMoment?.src"
                        x-bind:alt="selectedMoment?.alt"
                        class="gallery-detail-image"
                    />
                </div>

                <div class="gallery-detail-copy">
                    <p class="gallery-detail-kicker">Gallery Moment</p>
                    <h3 id="gallery-detail-title" class="gallery-detail-title" x-text="selectedMoment?.title"></h3>
                    <div class="gallery-detail-user">
                        <span>@</span>
                        <p x-text="selectedMoment?.username"></p>
                    </div>
                    <p class="gallery-detail-description" x-text="selectedMoment?.description"></p>
                </div>
            </div>
        </div>
    </div>
</div>
