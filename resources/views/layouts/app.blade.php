@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        @include('partials.head')
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Squada+One&display=swap" rel="stylesheet" />
        <meta name="color-scheme" content="light dark" />
        <script>
            (() => {
                const saved = localStorage.getItem('dashboard-theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                if (saved === 'dark' || (! saved && prefersDark)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>

    <body class="min-h-screen bg-slate-100 text-slate-800 antialiased transition-colors dark:bg-slate-950 dark:text-slate-100">
        @php
            $resourceGroups = \App\Support\DashboardResourceRegistry::navigation();
            $resourceGroups = collect($resourceGroups)
                ->map(function (array $group) {
                    if (($group['heading'] ?? null) === 'Website') {
                        $group['items'][] = [
                            'key' => 'user',
                            'label' => 'Users',
                            'icon' => 'user',
                            'route' => route('dashboard.user'),
                        ];
                    }

                    if (($group['heading'] ?? null) === 'Media & Partnership') {
                        $group['items'][] = [
                            'key' => 'landing-marquee',
                            'label' => 'Landing Marquees',
                            'icon' => 'list-bullet',
                            'route' => route('dashboard.landing-marquee'),
                        ];
                        $group['items'][] = [
                            'key' => 'landing-section-heading',
                            'label' => 'Section Headings',
                            'icon' => 'document-text',
                            'route' => route('dashboard.landing-section-heading'),
                        ];
                        $group['items'][] = [
                            'key' => 'song',
                            'label' => 'Songs',
                            'icon' => 'music-note',
                            'route' => route('dashboard.song'),
                        ];
                        $group['items'][] = [
                            'key' => 'youtube',
                            'label' => 'YouTube Videos',
                            'icon' => 'play',
                            'route' => route('dashboard.youtube'),
                        ];
                    }

                    if (($group['heading'] ?? null) === 'Landing Content') {
                        $group['items'][] = [
                            'key' => 'rundown-map',
                            'label' => 'Rundown & Map',
                            'icon' => 'map',
                            'route' => route('dashboard.rundown-map'),
                        ];
                    }

                    return $group;
                })
                ->values()
                ->all();
            $currentResource = request()->route('resource');
            $user = auth()->user();
            $avatarUrl = 'https://ui-avatars.com/api/?name='.rawurlencode($user?->name ?? 'Admin').'&background=4f46e5&color=fff';
            $currentResourceLabel = $currentResource
                ? collect($resourceGroups)->flatMap(fn ($group) => $group['items'])->firstWhere('key', $currentResource)['label'] ?? null
                : null;
            $pageTitle = filled($title ?? null)
                ? $title
                : ($currentResourceLabel ?? 'Dashboard');
        @endphp

        <div id="dashboardSidebarOverlay" class="fixed inset-0 z-40 hidden bg-slate-950/55 backdrop-blur-sm lg:hidden" data-dashboard-sidebar-overlay></div>

        <aside id="dashboardSidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-300 dark:border-slate-800 dark:bg-slate-900 lg:translate-x-0" data-dashboard-sidebar>
            <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-6 dark:border-slate-800">
                <a href="{{ route('dashboard') }}" wire:navigate class="flex min-w-0 items-center gap-3">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white shadow-lg shadow-indigo-500/25">
                        <x-ui-dashboard.icon name="rectangle-stack" class="h-6 w-6" />
                    </span>
                    <span class="min-w-0">
                        <span class="block truncate text-lg font-extrabold tracking-tight">Purnama Panel</span>
                        <span class="block truncate text-xs text-slate-500 dark:text-slate-400">Admin Dashboard</span>
                    </span>
                </a>

                <button type="button" class="ml-auto rounded-xl p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-800 lg:hidden" data-dashboard-sidebar-close aria-label="Tutup sidebar">
                    <x-ui-dashboard.icon name="x" class="h-5 w-5" />
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6" data-dashboard-sidebar-nav>
                <p class="mb-3 px-3 text-[11px] font-bold uppercase tracking-[.18em] text-slate-400">Overview</p>
                <a
                    href="{{ route('dashboard') }}"
                    wire:navigate
                    class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }} flex items-center gap-3 rounded-2xl px-4 py-3 font-semibold transition"
                >
                    <x-ui-dashboard.icon name="home" class="h-5 w-5" />
                    Dashboard
                </a>

                @foreach ($resourceGroups as $group)
                    <p class="mb-3 mt-7 px-3 text-[11px] font-bold uppercase tracking-[.18em] text-slate-400">{{ $group['heading'] }}</p>

                    @foreach ($group['items'] as $item)
                        @php($isCurrent = $currentResource === $item['key'])
                        <a
                            href="{{ $item['route'] ?? route('dashboard.resource', $item['key']) }}"
                            wire:navigate
                            class="{{ $isCurrent ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-500/10 dark:text-indigo-300' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white' }} group flex items-center gap-3 rounded-2xl px-4 py-3 font-medium transition"
                        >
                            <x-ui-dashboard.icon :name="$item['icon'] ?? 'circle'" class="{{ $isCurrent ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-400 group-hover:text-indigo-500' }} h-5 w-5" />
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                @endforeach
            </nav>

            <div class="space-y-3 border-t border-slate-200 p-4 dark:border-slate-800">
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-3 rounded-2xl px-4 py-3 font-medium text-slate-600 transition hover:bg-slate-100 hover:text-slate-950 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-white">
                    <x-ui-dashboard.icon name="globe-alt" class="h-5 w-5 text-slate-400" />
                    Lihat Landing Page
                </a>

                <div class="flex items-center gap-3 rounded-2xl bg-slate-50 p-3 dark:bg-slate-800/70">
                    <img src="{{ $avatarUrl }}" class="h-10 w-10 rounded-xl object-cover" alt="Avatar {{ $user?->name ?? 'Admin' }}" />
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-bold">{{ $user?->name ?? 'Admin' }}</p>
                        <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $user?->email }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg p-2 text-slate-400 hover:bg-white hover:text-rose-500 dark:hover:bg-slate-700" aria-label="Log out">
                            <x-ui-dashboard.icon name="logout" class="h-5 w-5" />
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="min-h-screen lg:pl-72">
            <header class="sticky top-0 z-30 border-b border-slate-200/80 bg-white/80 backdrop-blur-xl dark:border-slate-800 dark:bg-slate-900/80">
                <div class="flex h-20 items-center gap-3 px-4 sm:px-6 lg:px-8">
                    <button type="button" class="rounded-xl border border-slate-200 p-2.5 text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 lg:hidden" data-dashboard-sidebar-open aria-label="Buka sidebar">
                        <x-ui-dashboard.icon name="bars" class="h-5 w-5" />
                    </button>

                    <div class="min-w-0">
                        <h1 class="truncate text-xl font-extrabold tracking-tight">{{ $pageTitle }}</h1>
                        <p class="hidden text-xs text-slate-500 dark:text-slate-400 sm:block">Selamat datang kembali, {{ $user?->name ?? 'Admin' }}.</p>
                    </div>

                    <div class="ml-auto flex items-center gap-2">
                        <button type="button" class="grid h-11 w-11 place-items-center rounded-xl border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700" data-dashboard-theme-toggle aria-label="Ganti tema">
                            <svg class="hidden h-5 w-5 dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="4" />
                                <path d="M12 2v2M12 20v2M4.93 4.93l1.42 1.42M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.42-1.42M17.66 6.34l1.41-1.41" />
                            </svg>
                            <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 12.8A9 9 0 1 1 11.2 3 7 7 0 0 0 21 12.8Z" />
                            </svg>
                        </button>

                        <div class="flex items-center rounded-xl border border-slate-200 bg-white p-1.5 shadow-sm dark:border-slate-700 dark:bg-slate-800">
                            <img src="{{ $avatarUrl }}" class="h-8 w-8 rounded-lg" alt="Avatar" />
                        </div>
                    </div>
                </div>
            </header>

            <main class="space-y-6 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @if (session('status'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    window.Flux?.toast({
                        variant: 'success',
                        text: @js(session('status')),
                    });
                }, { once: true });
            </script>
        @endif

        @fluxScripts
        <script>
            (() => {
                const sidebarScrollKey = 'purnama-dashboard-sidebar-scroll-top';

                const setSidebar = (open) => {
                    const sidebar = document.querySelector('[data-dashboard-sidebar]');
                    const overlay = document.querySelector('[data-dashboard-sidebar-overlay]');

                    sidebar?.classList.toggle('-translate-x-full', ! open);
                    overlay?.classList.toggle('hidden', ! open);
                };

                const sidebarNav = () => document.querySelector('[data-dashboard-sidebar-nav]');

                const saveSidebarScroll = () => {
                    const nav = sidebarNav();

                    if (! nav) {
                        return;
                    }

                    sessionStorage.setItem(sidebarScrollKey, String(nav.scrollTop));
                };

                const restoreSidebarScroll = () => {
                    const nav = sidebarNav();

                    if (! nav) {
                        return;
                    }

                    const savedScrollTop = Number(sessionStorage.getItem(sidebarScrollKey) || 0);

                    requestAnimationFrame(() => {
                        nav.scrollTop = savedScrollTop;
                    });
                };

                const bindDashboardShell = () => {
                    const nav = sidebarNav();

                    if (nav && nav.dataset.dashboardScrollBound !== 'true') {
                        nav.dataset.dashboardScrollBound = 'true';
                        nav.addEventListener('scroll', saveSidebarScroll, { passive: true });
                    }

                    document.querySelectorAll('[data-dashboard-sidebar-open]').forEach((button) => {
                        if (button.dataset.dashboardBound === 'true') {
                            return;
                        }

                        button.dataset.dashboardBound = 'true';
                        button.addEventListener('click', () => setSidebar(true));
                    });

                    document.querySelectorAll('[data-dashboard-sidebar-close], [data-dashboard-sidebar-overlay]').forEach((button) => {
                        if (button.dataset.dashboardBound === 'true') {
                            return;
                        }

                        button.dataset.dashboardBound = 'true';
                        button.addEventListener('click', () => setSidebar(false));
                    });

                    document.querySelectorAll('[data-dashboard-theme-toggle]').forEach((button) => {
                        if (button.dataset.dashboardBound === 'true') {
                            return;
                        }

                        button.dataset.dashboardBound = 'true';
                        button.addEventListener('click', () => {
                            document.documentElement.classList.toggle('dark');
                            localStorage.setItem('dashboard-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
                        });
                    });

                    restoreSidebarScroll();
                };

                bindDashboardShell();

                if (window.__purnamaDashboardShellBound !== true) {
                    window.__purnamaDashboardShellBound = true;

                    document.addEventListener('livewire:navigate', saveSidebarScroll);
                    document.addEventListener('livewire:navigated', bindDashboardShell);
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape') {
                            setSidebar(false);
                        }
                    });
                }
            })();
        </script>
    </body>
</html>
