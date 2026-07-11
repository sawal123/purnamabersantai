@php
    $logoPath = $landingSetting?->logo_path;
    $logoUrl = $logoPath
        ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, '/') ? $logoPath : asset($logoPath))
        : asset('landing/assets/logo.png');

    $eventInfo = collect($landingSetting?->event_info ?? []);
    $eventDate = $eventInfo->get('date') ?? $eventInfo->get('tanggal') ?? 'Event date coming soon';
    $eventVenue = $eventInfo->get('venue') ?? $eventInfo->get('lokasi') ?? $eventInfo->get('location') ?? 'Venue coming soon';
    $eventGate = $eventInfo->get('open_gate') ?? $eventInfo->get('gate') ?? null;
    $footerHeadline = $landingSetting?->footer_description
        ?: ($landingSetting?->hero_tagline ?: 'Purnama Bersantai menghadirkan ruang temu untuk musik, komunitas, dan karya lokal.');
    $footerLinks = [
        ['label' => 'Home', 'href' => route('home')],
        ['label' => 'Lineup', 'href' => route('landing.lineup')],
        ['label' => 'Gallery', 'href' => route('landing.gallery')],
        ['label' => 'Sponsor & Partners', 'href' => route('landing.sponsors')],
        ['label' => 'Contact Us', 'href' => route('landing.contact')],
    ];
    $socialChannels = $contactChannels
        ->filter(fn ($channel) => filled($channel->url) && in_array($channel->type, ['instagram', 'tiktok', 'website', 'email', 'whatsapp'], true))
        ->take(5);
    $footerBackground = collect(glob(public_path('landing/assets/footer/*.{png,jpg,jpeg,webp,svg}'), GLOB_BRACE) ?: [])
        ->sort()
        ->map(fn (string $path) => asset('landing/assets/footer/'.basename($path)))
        ->first();
@endphp

<footer class="footer-simple relative overflow-hidden py-12 text-white">
    @if ($footerBackground)
        <div
            class="footer-simple-bg"
            style="background-image: url('{{ $footerBackground }}')"
            aria-hidden="true"
        ></div>
    @endif

    <div class="relative z-10 mx-auto max-w-7xl px-5 lg:px-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-start">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $landingSetting?->site_name ?? 'Purnama Bersantai' }} home" wire:navigate>
                <img
                    src="{{ $logoUrl }}"
                    alt="{{ $landingSetting?->site_name ?? 'Purnama Bersantai' }} logo"
                    class="h-20 w-20 rounded-full object-cover"
                />
            </a>

            <div class="font-display text-3xl uppercase tracking-[0.05em] text-white sm:text-4xl">
                <p>
                    {{ $eventDate }}
                    @if ($eventGate)
                        <span class="mx-2 text-[#fff700]">-</span>{{ $eventGate }}
                    @endif
                </p>
                <p class="mt-1 text-white/90">{{ $eventVenue }}</p>
            </div>
        </div>

        <h2 class="mt-10 max-w-5xl font-display text-5xl uppercase leading-none tracking-[0.04em] text-white sm:text-6xl">
            {{ $footerHeadline }}
        </h2>

        <div class="mt-10 border-y border-white/12 py-7">
            <div class="flex flex-col gap-8 xl:flex-row xl:items-center xl:justify-between">
                <nav class="flex flex-wrap gap-x-9 gap-y-4" aria-label="Footer navigation">
                    @foreach ($footerLinks as $link)
                        <a
                            href="{{ $link['href'] }}"
                            class="group inline-flex items-center gap-2 font-display text-2xl uppercase tracking-[0.05em] text-white transition hover:text-[#fff700]"
                            wire:navigate
                        >
                            {{ $link['label'] }}
                            <svg class="size-4 transition group-hover:translate-x-1 group-hover:-translate-y-1" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M7 17 17 7M9 7h8v8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                    @endforeach
                </nav>

                @if ($socialChannels->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-4">
                        <p class="font-display text-xl uppercase tracking-[0.08em] text-white">Follow Us</p>
                        <div class="flex flex-wrap items-center gap-3">
                            @foreach ($socialChannels as $channel)
                                <a
                                    href="{{ $channel->url }}"
                                    class="inline-flex size-10 items-center justify-center rounded-full border border-white/14 text-white/78 transition hover:border-[#fff700] hover:bg-[#fff700] hover:text-[#111111]"
                                    aria-label="{{ $channel->label }}"
                                    target="_blank"
                                    rel="noreferrer"
                                >
                                    <x-contact-channel-icon :icon="$channel->icon" :type="$channel->type" class="size-5" />
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="mt-8 flex flex-col gap-3 text-sm font-semibold uppercase tracking-[0.05em] text-white/70 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} {{ $landingSetting?->site_name ?? 'Purnama Bersantai' }}</p>
            <p>{{ $landingSetting?->sponsor_text ?? 'Official festival information and partnership updates.' }}</p>
        </div>
    </div>
</footer>
