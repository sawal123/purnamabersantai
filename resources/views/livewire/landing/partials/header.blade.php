@php
    $navItems = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Merchandise', 'href' => route('landing.merch'), 'active' => request()->routeIs('landing.merch')],
        ['label' => 'Lineup', 'href' => route('landing.lineup'), 'active' => request()->routeIs('landing.lineup')],
        ['label' => 'Gallery', 'href' => route('landing.gallery'), 'active' => request()->routeIs('landing.gallery')],
        ['label' => 'Sponsor & Partner', 'href' => route('landing.sponsors'), 'active' => request()->routeIs('landing.sponsors')],
    ];
    $moreNavItems = [
        ['label' => 'Playlist', 'href' => route('landing.playlist'), 'active' => request()->routeIs('landing.playlist')],
        ['label' => 'Rundown & Map', 'href' => route('landing.rundown-map'), 'active' => request()->routeIs('landing.rundown-map')],
        ['label' => 'About Us', 'href' => route('landing.about'), 'active' => request()->routeIs('landing.about')],
        ['label' => 'Contact Us', 'href' => route('landing.contact'), 'active' => request()->routeIs('landing.contact')],
        ['label' => 'FAQ', 'href' => route('landing.faq'), 'active' => request()->routeIs('landing.faq')],
    ];
    $mobileNavItems = [...$navItems, ...$moreNavItems];
    $isMoreActive = collect($moreNavItems)->contains(fn ($item) => $item['active']);
    $logoPath = $landingSetting?->logo_path;
    $logoUrl = $logoPath
        ? (str_starts_with($logoPath, 'http') || str_starts_with($logoPath, '/') ? $logoPath : asset($logoPath))
        : asset('landing/assets/logo.png');
@endphp

<header id="site-header" class="site-header">
    <div class="site-header-shell">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-5 lg:px-8">
            <div class="flex items-center justify-between gap-3">
                <a
                    href="{{ route('home') }}"
                    class="shrink-0 transition hover:opacity-90"
                    aria-label="Purnama Bersantai home"
                    wire:navigate
                >
                    <img
                        src="{{ $logoUrl }}"
                        alt="{{ $landingSetting?->site_name ?? 'Purnama Bersantai' }} logo"
                        class="site-header-logo"
                    />
                </a>

                <nav
                    class="hidden items-center gap-7 text-sm font-semibold uppercase tracking-[0.18em] lg:flex"
                >
                    @foreach ($navItems as $item)
                        <a
                            href="{{ $item['href'] }}"
                            class="transition hover:text-[#fff700] {{ $item['active'] ? 'text-[#fff700]' : 'text-white/85' }}"
                            wire:navigate
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div
                        class="relative"
                        x-data="{ open: false }"
                        @mouseenter="open = true"
                        @mouseleave="open = false"
                        @focusin="open = true"
                        @focusout="open = false"
                    >
                        <button
                            type="button"
                            class="desktop-more-trigger {{ $isMoreActive ? 'text-[#fff700]' : 'text-white/85' }}"
                            :class="{ 'text-[#fff700]': open }"
                            :aria-expanded="open.toString()"
                            aria-haspopup="true"
                        >
                            <span>More</span>
                            <svg
                                class="desktop-more-icon"
                                :class="{ 'rotate-180': open }"
                                width="16"
                                height="16"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                            class="desktop-more-menu"
                        >
                            @foreach ($moreNavItems as $item)
                                <a
                                    href="{{ $item['href'] }}"
                                    class="desktop-more-link {{ $item['active'] ? 'text-[#fff700]' : 'text-white/85' }}"
                                    wire:navigate
                                >
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </nav>

                <button
                    id="mobile-menu-button"
                    type="button"
                    class="inline-flex h-12 w-12 items-center justify-center rounded-xl border border-white/15 bg-white/5 text-white transition hover:border-white/30 hover:bg-white/10 lg:hidden"
                    aria-expanded="false"
                    aria-controls="mobile-nav-panel"
                    aria-label="Open navigation menu"
                >
                    <span class="mobile-menu-icon" aria-hidden="true">
                        <span class="mobile-menu-icon-line"></span>
                        <span class="mobile-menu-icon-line"></span>
                        <span class="mobile-menu-icon-line"></span>
                    </span>
                </button>
            </div>

            <div id="mobile-nav-panel" class="mobile-nav-panel lg:hidden">
                <div class="mobile-nav-inner">
                    <nav class="space-y-1">
                        @foreach ($mobileNavItems as $item)
                            <a href="{{ $item['href'] }}" class="mobile-nav-link" wire:navigate>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>
