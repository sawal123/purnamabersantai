@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $heading = ($landingSectionHeadings ?? collect())->get('history');
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="text-center">
                    <p class="landing-heading-kicker">
                        {{ $heading?->kicker ?: 'Festival History' }}
                    </p>
                    <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Semua Perjalanan Purnama Bersantai'])
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                        {{ $heading?->subtitle ?: 'Telusuri rangkaian acara, momen, dan perkembangan Purnama Bersantai dari tahun ke tahun.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <section class="reveal overflow-hidden rounded-[1.9rem] border border-white/10 bg-[#2f2e2e] p-6 sm:p-8">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                                Full Archive
                            </p>
                            <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl">
                                Arsip History
                            </h2>
                        </div>

                        <a href="{{ route('landing.about') }}" class="inline-flex w-fit rounded-2xl border-2 border-white/15 px-6 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10" wire:navigate>
                            About
                        </a>
                    </div>

                    <div class="relative mt-10">
                        <div class="space-y-6">
                            @forelse ($festivalHistory as $item)
                                <article class="relative grid gap-4 md:grid-cols-[7rem_minmax(0,1fr)] md:gap-6">
                                    <div class="relative flex items-start justify-center md:justify-center">
                                        @if (! $loop->last)
                                            <span class="absolute left-1/2 top-[3.4rem] hidden h-[calc(100%+1.5rem)] w-px -translate-x-1/2 bg-gradient-to-b from-[#fff700]/55 via-white/18 to-transparent md:block"></span>
                                        @endif
                                        <div class="inline-flex rounded-full border border-[#fff700]/25 bg-[#fff700]/10 px-4 py-2 font-display text-2xl uppercase tracking-[0.08em] text-[#fff700] md:min-w-[5.5rem] md:justify-center">
                                            {{ $item['year'] }}
                                        </div>
                                    </div>

                                    <a href="{{ route('landing.history.show', ['title' => $item['slug']]) }}" class="grid overflow-hidden rounded-[1.6rem] border border-white/10 bg-[#2f2e2e] transition hover:-translate-y-1 hover:bg-[#242323] sm:grid-cols-[14rem_minmax(0,1fr)]" wire:navigate>
                                        <div class="relative min-h-52 sm:min-h-full">
                                            <img
                                                src="{{ $imageUrl($item['thumbnail'] ?? null, asset('landing/assets/Rectangle 17.png')) }}"
                                                alt="{{ $item['title'] }}"
                                                class="absolute inset-0 h-full w-full object-cover"
                                            />
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/45 to-transparent sm:hidden"></div>
                                        </div>

                                        <div class="p-5">
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <h3 class="font-display text-3xl uppercase tracking-[0.08em] text-white">
                                                        {{ $item['title'] }}
                                                    </h3>
                                                    <p class="mt-2 text-sm font-semibold uppercase tracking-[0.2em] text-[#fff700]/80">
                                                        {{ $item['location'] }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-white/10 bg-white/6 text-white/80">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                                                        <path d="M7 17L17 7M17 7H8.5M17 7V15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>

                                            <p class="mt-4 text-base leading-relaxed text-white/68">
                                                {{ $item['summary'] }}
                                            </p>
                                        </div>
                                    </a>
                                </article>
                            @empty
                                <div class="rounded-[1.6rem] border border-white/10 bg-[#2f2e2e] px-6 py-12 text-center">
                                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                                        Belum Ada History
                                    </p>
                                    <h3 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white">
                                        Arsip akan segera hadir
                                    </h3>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
