<section class="w-full space-y-6">
    @php
        $completedChecks = $healthChecks->where('is_complete', true)->count();
        $totalChecks = $healthChecks->count();
    @endphp

    <div class="overflow-hidden rounded-3xl border border-zinc-200 bg-zinc-950 text-white shadow-sm dark:border-zinc-700">
        <div class="grid gap-8 p-6 lg:grid-cols-[minmax(0,1.45fr)_minmax(20rem,0.8fr)] lg:p-8">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-amber-300">Control Center</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight md:text-4xl">Ringkasan konten Purnama Bersantai</h1>
                <p class="mt-4 text-sm leading-6 text-zinc-300 md:text-base">
                    Pantau kesiapan landing page, SEO, ticketing, lineup, media, dan data penting lain dari satu tempat sebelum masuk ke halaman CRUD.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    @foreach ($quickActions->take(3) as $action)
                        <a
                            href="{{ $action['url'] }}"
                            wire:navigate
                            class="inline-flex items-center gap-2 rounded-2xl bg-white px-4 py-2.5 text-sm font-semibold text-zinc-950 transition hover:-translate-y-0.5 hover:bg-amber-100"
                        >
                            {{ $action['label'] }}
                            <flux:icon.arrow-right class="size-4" />
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2">
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">Readiness</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $contentReadiness }}%</p>
                    <div class="mt-4 h-2 overflow-hidden rounded-full bg-white/10">
                        <div class="h-full rounded-full bg-amber-300" style="width: {{ $contentReadiness }}%"></div>
                    </div>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">Health Check</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $completedChecks }}/{{ $totalChecks }}</p>
                    <p class="mt-3 text-xs text-zinc-400">Item penting sudah aktif</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">Active Records</p>
                    <p class="mt-2 text-3xl font-semibold">{{ $activeRecordCount }}</p>
                    <p class="mt-3 text-xs text-zinc-400">dari {{ $recordCount }} total record</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-400">Last Update</p>
                    <p class="mt-2 text-xl font-semibold">
                        {{ $lastUpdatedAt ? \Illuminate\Support\Carbon::parse($lastUpdatedAt)->diffForHumans() : 'Belum ada' }}
                    </p>
                    <p class="mt-3 text-xs text-zinc-400">{{ $moduleCount }} modul terpantau</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Modules</p>
            <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $activeModuleCount }}/{{ $moduleCount }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">modul punya data aktif</p>
        </div>
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Published</p>
            <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $activeRecordCount }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">record aktif di landing</p>
        </div>
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Inactive</p>
            <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $inactiveRecordCount }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">record draft/nonaktif</p>
        </div>
        <div class="rounded-3xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Groups</p>
            <p class="mt-3 text-3xl font-semibold text-zinc-950 dark:text-white">{{ $groupCount }}</p>
            <p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">area konten dashboard</p>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_minmax(22rem,0.8fr)]">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Prioritas</p>
                    <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Kesiapan landing page</h2>
                </div>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $completedChecks }} dari {{ $totalChecks }} checklist selesai</p>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-2">
                @foreach ($healthChecks as $check)
                    <a
                        href="{{ $check['url'] ?? '#' }}"
                        wire:navigate
                        class="rounded-2xl border p-4 transition hover:-translate-y-0.5 {{ $check['is_complete'] ? 'border-emerald-200 bg-emerald-50 dark:border-emerald-500/20 dark:bg-emerald-500/10' : 'border-amber-200 bg-amber-50 dark:border-amber-500/20 dark:bg-amber-500/10' }}"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <h3 class="font-semibold text-zinc-950 dark:text-white">{{ $check['title'] }}</h3>
                                <p class="mt-2 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $check['description'] }}</p>
                            </div>
                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $check['is_complete'] ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-200' }}">
                                {{ $check['is_complete'] ? 'Ready' : 'Needs data' }}
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Perlu Dilengkapi</p>
            <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Konten yang masih kosong</h2>

            <div class="mt-6 space-y-3">
                @forelse ($needsAttention as $item)
                    <a href="{{ route('dashboard.resource', $item['key']) }}" wire:navigate class="flex items-center justify-between gap-4 rounded-2xl bg-zinc-50 p-4 transition hover:bg-amber-50 dark:bg-zinc-950/60 dark:hover:bg-amber-500/10">
                        <div>
                            <p class="font-semibold text-zinc-950 dark:text-white">{{ $item['label'] }}</p>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $item['reason'] }}</p>
                        </div>
                        <flux:icon.arrow-up-right class="size-5 text-zinc-400" />
                    </a>
                @empty
                    <div class="rounded-2xl bg-emerald-50 p-4 text-sm leading-6 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-200">
                        Semua modul utama sudah punya data aktif.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[minmax(22rem,0.8fr)_minmax(0,1.2fr)]">
        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Quick Actions</p>
            <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Akses cepat</h2>

            <div class="mt-6 grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                @foreach ($quickActions as $action)
                    <a href="{{ $action['url'] }}" wire:navigate class="rounded-2xl border border-zinc-200 p-4 transition hover:-translate-y-0.5 hover:border-amber-300 hover:bg-amber-50 dark:border-zinc-700 dark:hover:border-amber-400 dark:hover:bg-amber-500/10">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="font-semibold text-zinc-950 dark:text-white">{{ $action['label'] }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $action['description'] }}</p>
                            </div>
                            <flux:icon.arrow-right class="size-5 text-zinc-400" />
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Status Area</p>
            <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Ringkasan per group</h2>

            <div class="mt-6 space-y-4">
                @foreach ($groups as $group)
                    <div class="rounded-2xl bg-zinc-50 p-4 dark:bg-zinc-950/60">
                        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-semibold text-zinc-950 dark:text-white">{{ $group['heading'] }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $group['module_count'] }} modul, {{ $group['active_count'] }} dari {{ $group['count'] }} record aktif</p>
                            </div>
                            <p class="text-xl font-semibold text-zinc-950 dark:text-white">{{ $group['completion'] }}%</p>
                        </div>
                        <div class="mt-4 h-2 overflow-hidden rounded-full bg-zinc-200 dark:bg-zinc-800">
                            <div class="h-full rounded-full bg-amber-400" style="width: {{ $group['completion'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-700 dark:bg-zinc-900">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-amber-700 dark:text-amber-300">Recent Updates</p>
                <h2 class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">Perubahan terbaru</h2>
            </div>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Diambil dari record terakhir tiap modul</p>
        </div>

        <div class="mt-6 divide-y divide-zinc-200 overflow-hidden rounded-2xl border border-zinc-200 dark:divide-zinc-800 dark:border-zinc-800">
            @forelse ($recentUpdates as $update)
                <a href="{{ $update['url'] }}" wire:navigate class="grid gap-2 p-4 transition hover:bg-amber-50 dark:hover:bg-amber-500/10 md:grid-cols-[12rem_minmax(0,1fr)_10rem] md:items-center">
                    <p class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $update['resource_label'] }}</p>
                    <p class="truncate text-sm text-zinc-600 dark:text-zinc-300">{{ $update['record_label'] }}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 md:text-right">{{ $update['updated_at']->diffForHumans() }}</p>
                </a>
            @empty
                <div class="p-4 text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada perubahan konten yang bisa ditampilkan.
                </div>
            @endforelse
        </div>
    </div>
</section>
