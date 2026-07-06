@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section id="lineup" class="relative z-10 pb-20 pt-32">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">
                <div class="text-center">
                    <h1 class="font-display text-5xl uppercase tracking-[0.12em] text-white sm:text-6xl">
                        Lineup
                    </h1>
                    <p class="mx-auto mt-2 max-w-3xl text-lg text-white/70">
                        Temukan performer favoritmu dan jelajahi deretan artis yang akan menghidupkan panggung Purnama Bersantai.
                    </p>
                </div>

                <div class="mx-auto mt-10 max-w-3xl">
                    <div class="lineup-search-shell relative overflow-hidden rounded-[1.75rem] border border-white/12 bg-white/[0.04] p-3 shadow-[0_18px_48px_rgba(0,0,0,0.24)] backdrop-blur-xl">
                        <div class="flex items-center gap-3 rounded-[1.2rem] border border-white/10 bg-black/10 px-4 py-3">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 shrink-0 text-white/55" aria-hidden="true">
                                <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <input
                                type="text"
                                placeholder="Search artist name..."
                                class="min-w-0 flex-1 bg-transparent text-base text-white outline-none placeholder:text-white/35"
                                wire:model.live.debounce.350ms="search"
                            />

                            <div class="flex items-center gap-2">
                                @if ($search !== '')
                                    <button
                                        type="button"
                                        class="text-xs font-semibold uppercase tracking-[0.16em] text-white/45 transition hover:text-white"
                                        wire:click="$set('search', '')"
                                    >
                                        Clear
                                    </button>
                                @endif

                                <div wire:loading wire:target="search" class="flex items-center">
                                    <span class="lineup-spinner h-5 w-5 rounded-full border-2 border-white/20 border-t-amber-300"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center text-sm text-white/50">
                    @if ($search !== '')
                        Menampilkan {{ $lineupArtists->count() }} dari {{ $totalArtists }} hasil untuk "<span class="text-white/80">{{ $search }}</span>"
                    @else
                        Menampilkan {{ $lineupArtists->count() }} artist dari total {{ $totalArtists }} lineup
                    @endif
                </div>

                <div class="relative mt-10">
                    <div wire:loading.flex wire:target="search" class="absolute inset-0 z-10 items-center justify-center rounded-[2rem] bg-black/20 backdrop-blur-[2px]">
                        <div class="flex items-center gap-3 rounded-full border border-white/12 bg-black/35 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white">
                            <span class="lineup-spinner h-5 w-5 rounded-full border-2 border-white/20 border-t-amber-300"></span>
                            Searching
                        </div>
                    </div>

                    @if ($lineupArtists->isNotEmpty())
                        <div class="lineup-grid flex flex-wrap justify-center gap-y-5">
                            @foreach ($lineupArtists as $artist)
                                <div class="w-1/2 px-2.5 lg:w-1/4">
                                    <article
                                        wire:key="lineup-artist-{{ $artist->id }}"
                                        class="lineup-card relative isolate aspect-[4/5] overflow-hidden rounded-[1.75rem] shadow-glow transition duration-300"
                                    >
                                        <div class="lineup-media">
                                            <img
                                                src="{{ $imageUrl($artist->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                                                alt="{{ $artist->alt_text ?: $artist->name }}"
                                                class="{{ $artist->image_class ?: 'object-cover' }}"
                                            />
                                        </div>
                                        <div class="glass-name rounded-2xl px-3 py-2 text-center">
                                            <p class="font-display text-[1.35rem] uppercase tracking-[0.08em] text-white sm:text-[1.55rem]">
                                                {{ $artist->name }}
                                            </p>
                                        </div>
                                    </article>
                                </div>
                            @endforeach
                        </div>

                        @if ($hasMoreArtists)
                            <div class="mt-10 flex justify-center">
                                <button
                                    type="button"
                                    class="inline-flex min-w-[12rem] items-center justify-center gap-3 rounded-2xl border border-white/12 bg-white/6 px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-60"
                                    wire:click="loadMore"
                                    wire:loading.attr="disabled"
                                    wire:target="loadMore"
                                >
                                    <span wire:loading.remove wire:target="loadMore">More</span>
                                    <span wire:loading wire:target="loadMore" class="inline-flex items-center gap-3">
                                        <span class="lineup-spinner h-5 w-5 rounded-full border-2 border-white/20 border-t-amber-300"></span>
                                        Loading
                                    </span>
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="rounded-[2rem] border border-white/10 bg-white/5 px-6 py-14 text-center shadow-[0_18px_48px_rgba(0,0,0,0.22)]">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                                No Result
                            </p>
                            <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl">
                                Artist Not Found
                            </h2>
                            <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/65">
                                Tidak ada artist yang cocok dengan pencarianmu. Coba gunakan kata kunci lain.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto max-w-5xl px-5 text-center lg:px-8">
                <div class="rounded-[1.75rem] border border-white/10 bg-white/5 px-6 py-8 shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                        Next Stop
                    </p>
                    <h2 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        Continue to Tickets
                    </h2>
                    <p class="mx-auto mt-3 max-w-2xl text-lg leading-relaxed text-white/70">
                        Setelah pengunjung melihat performer, arahkan mereka ke halaman ticketing untuk konversi yang lebih jelas.
                    </p>
                    <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">
                        <a href="{{ route('landing.tickets') }}" class="rounded-2xl bg-ember px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-red-500" wire:navigate>Buy Ticket</a>
                        <a href="{{ route('landing.gallery') }}" class="rounded-2xl border-2 border-ember px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10" wire:navigate>View Gallery</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
