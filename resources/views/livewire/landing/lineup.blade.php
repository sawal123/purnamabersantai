@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
@endphp

<div
    class="relative overflow-x-hidden"
    x-data="{ selectedArtist: null }"
    x-effect="document.body.classList.toggle('modal-open', selectedArtist !== null)"
    x-on:keydown.escape.window="selectedArtist = null"
>
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
                    <div class="lineup-search-shell relative overflow-hidden rounded-[1.75rem] border border-[#fff700]/45 bg-[#2f2e2e]/70 p-3 shadow-[0_22px_60px_rgba(47,46,46,0.34),0_0_34px_rgba(255,247,0,0.16)] backdrop-blur-xl transition focus-within:border-[#fff700] focus-within:shadow-[0_24px_68px_rgba(47,46,46,0.42),0_0_42px_rgba(255,247,0,0.28)]">
                        <div class="flex items-center gap-3 rounded-[1.2rem] border border-[#ff9f3c]/35 bg-gradient-to-r from-[#2f2e2e] via-[#4a2a19] to-[#ec5b00]/70 px-4 py-3 transition focus-within:border-[#fff700]/75">
                            <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5 shrink-0 text-[#fff700]" aria-hidden="true">
                                <path d="M21 21L16.65 16.65M19 11C19 15.4183 15.4183 19 11 19C6.58172 19 3 15.4183 3 11C3 6.58172 6.58172 3 11 3C15.4183 3 19 6.58172 19 11Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>

                            <input
                                type="text"
                                placeholder="Search artist name..."
                                class="min-w-0 flex-1 bg-transparent text-base font-medium text-white caret-[#fff700] outline-none placeholder:text-white/55"
                                wire:model.live.debounce.350ms="search"
                            />

                            <div class="flex items-center gap-2">
                                @if ($search !== '')
                                    <button
                                        type="button"
                                        class="text-xs font-semibold uppercase tracking-[0.16em] text-[#fff700]/80 transition hover:text-white"
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
                        <div class="lineup-grid lineup-page-grid flex flex-wrap justify-center gap-y-5">
                            @foreach ($lineupArtists as $artist)
                                @php
                                    $artistImage = $imageUrl($artist->image_path, asset('landing/assets/Rectangle 17.png'));
                                    $artistAlt = $artist->alt_text ?: $artist->name;
                                @endphp
                                <div class="w-1/2 px-2.5 lg:w-1/4">
                                    <button
                                        type="button"
                                        wire:key="lineup-artist-{{ $artist->id }}"
                                        class="lineup-card lineup-page-card relative isolate aspect-[4/5] w-full cursor-zoom-in overflow-hidden rounded-[1.75rem] transition duration-300"
                                        aria-label="Open full image for {{ $artist->name }}"
                                        x-on:click="selectedArtist = {
                                            src: @js($artistImage),
                                            alt: @js($artistAlt),
                                            name: @js($artist->name)
                                        }"
                                    >
                                        <div class="lineup-media">
                                            <img
                                                src="{{ $artistImage }}"
                                                alt="{{ $artistAlt }}"
                                                class="{{ $artist->image_class ?: 'object-cover' }}"
                                            />
                                        </div>
                                        <div class="glass-name rounded-2xl px-3 py-2 text-center">
                                            <p class="font-display text-[1.35rem] uppercase tracking-[0.08em] text-white sm:text-[1.55rem]">
                                                {{ $artist->name }}
                                            </p>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @if ($hasMoreArtists)
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
                                        <span class="lineup-spinner h-5 w-5 rounded-full border-2 border-white/20 border-t-amber-300"></span>
                                        Loading
                                    </span>
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="rounded-[2rem] border border-[#fff700]/20 bg-[#2f2e2e] px-6 py-14 text-center shadow-[0_18px_48px_rgba(47,46,46,0.32)]">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
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

    </main>

    <div
        x-cloak
        x-show="selectedArtist"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[80] grid place-items-center p-4"
        aria-modal="true"
        role="dialog"
        aria-labelledby="lineup-detail-title"
    >
        <button
            type="button"
            class="absolute inset-0 cursor-zoom-out bg-black/75 backdrop-blur-md"
            aria-label="Close lineup image"
            x-on:click="selectedArtist = null"
        ></button>

        <div
            x-show="selectedArtist"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-6 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-180"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-6 scale-95"
            class="relative z-10 flex max-h-[92dvh] w-fit max-w-[94vw] flex-col overflow-hidden rounded-[1.5rem] border border-white/12 bg-[#2f2e2e]"
        >
            <button
                type="button"
                class="absolute right-4 top-4 z-20 inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/15 bg-[#2f2e2e]/80 text-3xl leading-none text-white backdrop-blur transition hover:bg-[#fff700] hover:text-[#2f2e2e]"
                aria-label="Close lineup image"
                x-on:click="selectedArtist = null"
            >
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="grid min-h-0 bg-black/20">
                <img
                    x-bind:src="selectedArtist?.src"
                    x-bind:alt="selectedArtist?.alt"
                    class="block max-h-[82dvh] max-w-[94vw] object-contain"
                />
            </div>

            <div class="border-t border-white/10 px-5 py-3 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">Lineup Artist</p>
                <h2 id="lineup-detail-title" class="mt-1 font-display text-3xl uppercase tracking-[0.08em] text-white sm:text-4xl" x-text="selectedArtist?.name"></h2>
            </div>
        </div>
    </div>

    @include('livewire.landing.partials.footer')
</div>
