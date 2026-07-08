@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;

    $fallbackHeroA = asset('landing/assets/hero/image 1.png');
    $fallbackHeroB = asset('landing/assets/hero/2022_03_29_124082_1648520536._large.jpg');
    $heroImageUrls = $heroImages->pluck('image_path')
        ->map(fn ($path) => $imageUrl($path, $fallbackHeroA))
        ->values();
    $heroImageUrls = $heroImageUrls->isNotEmpty() ? $heroImageUrls : collect([$fallbackHeroA, $fallbackHeroB]);
    $heroBrand = $imageUrl($landingSetting?->hero_brand_path, asset('landing/assets/3-cropped.png'));
    $countdownTarget = $countdownSetting?->target_at;
@endphp

<section id="home" class="relative isolate min-h-screen overflow-hidden">
    <script type="application/json" id="landing-hero-images-json">@json($heroImageUrls)</script>

    <div class="hero-visual" data-hero-parallax>
        <img
            id="hero-bg-a"
            src="{{ $heroImageUrls->first() }}"
            alt="{{ $heroImages->first()?->alt_text ?? 'Festival crowd' }}"
            class="hero-bg spotlight is-active"
        />
        <img
            id="hero-bg-b"
            src="{{ $heroImageUrls->get(1, $heroImageUrls->first()) }}"
            alt="{{ $heroImages->get(1)?->alt_text ?? 'Festival crowd' }}"
            class="hero-bg spotlight"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-black/55 via-black/10 to-midnight/80"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(255,221,153,0.28),transparent_30%),radial-gradient(circle_at_50%_35%,rgba(255,255,255,0.14),transparent_20%),linear-gradient(180deg,rgba(0,0,0,0.02)_0%,rgba(20,1,2,0.48)_100%)]"></div>
    </div>

    <div class="hero-shell relative z-10 mx-auto flex max-w-7xl flex-col justify-center px-5 text-center lg:px-8">
        <div class="hero-stack reveal is-visible mx-auto" style="--delay: 100ms">
            <img
                src="{{ $heroBrand }}"
                alt="{{ $landingSetting?->site_name ?? 'Purnama Bersantai' }} logo"
                class="hero-brand mx-auto mt-0 drop-shadow-[0_10px_60px_rgba(255,255,255,0.15)]"
            />

            @if ($countdownTarget)
                <div
                    class="hero-countdown"
                    data-countdown
                    data-countdown-target="{{ $countdownTarget->format('Y-m-d\TH:i:s') }}"
                    aria-label="Countdown menuju {{ $countdownSetting->title }}"
                >
                    @foreach ([['days', 'Hari'], ['hours', 'Jam'], ['minutes', 'Menit'], ['seconds', 'Detik']] as [$unit, $label])
                        <div class="hero-countdown-item">
                            <span class="hero-countdown-value" data-countdown-unit="{{ $unit }}">00</span>
                            <span class="hero-countdown-label">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <p class="hero-title reveal is-visible mt-4 font-display uppercase tracking-[0.08em] text-white sm:mt-5 lg:mt-6" style="--delay: 180ms">
            {{ $landingSetting?->hero_tagline ?? 'Where Music Meets the Moonlight' }}
        </p>
        <p class="hero-copy reveal is-visible mx-auto mt-2.5 font-medium text-white/75" style="--delay: 260ms">
            {{ $landingSetting?->hero_description ?? 'A vibrant night of sing-alongs, merch drops, and unforgettable memories under one stage.' }}
        </p>
        <div class="hero-actions reveal is-visible mt-5 flex flex-col items-center justify-center gap-4 sm:flex-row" style="--delay: 340ms">
            <a href="{{ route('landing.tickets') }}" class="rounded-2xl bg-ember font-display uppercase tracking-[0.08em] text-white shadow-lg shadow-[#2f2e2e]/50 transition hover:-translate-y-1 hover:bg-[#fff700] hover:text-[#2f2e2e]" wire:navigate>
                Buy Ticket
            </a>
            <a href="{{ route('landing.merch') }}" class="rounded-2xl bg-cobalt font-display uppercase tracking-[0.08em] text-white shadow-lg shadow-[#2f2e2e]/50 transition hover:-translate-y-1 hover:bg-[#fff700] hover:text-[#2f2e2e]" wire:navigate>
                Buy Merch
            </a>
        </div>
    </div>
</section>
