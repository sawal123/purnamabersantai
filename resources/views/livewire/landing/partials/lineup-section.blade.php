@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $lineupFrames = collect(glob(public_path('landing/assets/frame/*.{png,jpg,jpeg,webp,svg}'), GLOB_BRACE) ?: [])
        ->map(fn (string $path) => asset('landing/assets/frame/'.basename($path)))
        ->values();
    $lineupFrame = fn (mixed $seed) => $lineupFrames->isNotEmpty()
        ? $lineupFrames[abs(crc32((string) $seed)) % $lineupFrames->count()]
        : asset('landing/assets/frame/frame1.png');
    $heading = ($landingSectionHeadings ?? collect())->get('lineup');
@endphp

@include('livewire.landing.partials.lineup-marquee')

<section id="lineup" class="relative z-10 pb-20 {{ ($compactTop ?? false) ? 'pt-8' : 'pt-32' }}">
    @include('livewire.landing.partials.section-elements', ['pageSection' => 'lineup', 'offset' => 0, 'variant' => 'lineup'])

    <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
        <div class="reveal text-center">
            @if (filled($heading?->kicker))
                <p class="landing-heading-kicker mb-3">
                    {{ $heading->kicker }}
                </p>
            @endif
            <h2 class="font-display text-5xl uppercase tracking-[0.12em] text-white sm:text-6xl">
                @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Lineup'])
            </h2>
            <p class="mt-2 text-lg text-white/70">
                {{ $heading?->subtitle ?: 'Meet the artists taking the stage.' }}
            </p>
        </div>

        <div class="swiper content-swiper lineup-swiper reveal mt-10" style="--delay: 80ms">
            <div class="swiper-wrapper">
                @forelse ($lineupArtists as $artist)
                    <div class="swiper-slide">
                        <article
                            class="lineup-card relative isolate aspect-[4/5] overflow-hidden rounded-[1.75rem] shadow-glow transition duration-300 hover:-translate-y-2"
                            style="--lineup-frame-image: url('{{ $lineupFrame($artist->id) }}')"
                        >
                            <div class="lineup-media">
                                <img
                                    src="{{ $imageUrl($artist->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                                    alt="{{ $artist->alt_text ?: $artist->name }}"
                                    class="{{ $artist->image_class ?: 'object-cover' }}"
                                />
                            </div>
                            <div class="glass-name rounded-2xl px-3 py-2 text-center">
                                <p class="font-display text-[1.75rem] uppercase tracking-[0.08em] text-white">
                                    {{ $artist->name }}
                                </p>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="swiper-slide">
                        <article
                            class="lineup-card relative isolate aspect-[4/5] overflow-hidden rounded-[1.75rem] shadow-glow"
                            style="--lineup-frame-image: url('{{ $lineupFrame('coming-soon') }}')"
                        >
                            <div class="lineup-media">
                                <img src="{{ asset('landing/assets/Rectangle 17.png') }}" alt="Festival performer" />
                            </div>
                            <div class="glass-name rounded-2xl px-3 py-2 text-center">
                                <p class="font-display text-[1.75rem] uppercase tracking-[0.08em] text-white">
                                    Coming Soon
                                </p>
                            </div>
                        </article>
                    </div>
                @endforelse
            </div>
            <div class="section-slider-bar">
                <div class="section-slider-pagination lineup-pagination"></div>
                <div class="section-slider-nav">
                    <button type="button" class="section-slider-button lineup-prev" aria-label="Previous lineup slide">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="section-slider-button lineup-next" aria-label="Next lineup slide">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="reveal mt-10 text-center" style="--delay: 160ms">
            <a
                href="{{ route('landing.lineup') }}"
                class="inline-flex rounded-2xl border border-[#2f2e2e] bg-[#2f2e2e] px-8 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:border-[#fff700] hover:bg-[#fff700] hover:text-[#2f2e2e]"
                wire:navigate
            >
                See All Lineup
            </a>
        </div>
    </div>
</section>
