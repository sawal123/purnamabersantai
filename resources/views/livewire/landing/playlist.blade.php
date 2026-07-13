@php
    $heading = ($landingSectionHeadings ?? collect())->get('playlist');
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[36rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="text-center">
                    <p class="landing-heading-kicker">
                        {{ $heading?->kicker ?: 'Official Playlist' }}
                    </p>
                    <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Purnama Bersantai Sounds'])
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                        {{ $heading?->subtitle ?: 'Putar pilihan lagu untuk masuk ke suasana festival sebelum panggung dimulai.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto max-w-5xl px-5 lg:px-8">
                <article class="overflow-hidden rounded-[2rem] border border-white/10 bg-[#2f2e2e]/95 p-4 shadow-[0_24px_70px_rgba(0,0,0,0.28)] sm:p-6">
                    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#fff700]/80">Spotify</p>
                            <h2 class="mt-2 font-display text-4xl uppercase tracking-[0.08em] text-white">
                                Festival Warm Up
                            </h2>
                        </div>
                        <a
                            href="https://open.spotify.com/playlist/3ijdciOT0zodS2QVoUS0n3?si=26a07152a7914027"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex rounded-2xl border-2 border-ember px-5 py-2.5 font-display text-2xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10"
                        >
                            Open Spotify
                        </a>
                    </div>

                    <iframe
                        data-testid="embed-iframe"
                        title="Spotify playlist Purnama Bersantai"
                        class="h-[352px] w-full rounded-2xl"
                        src="https://open.spotify.com/embed/playlist/3ijdciOT0zodS2QVoUS0n3?utm_source=generator&theme=0&si=51050317e5c34125"
                        width="100%"
                        height="352"
                        frameborder="0"
                        allowfullscreen
                        allow="autoplay; clipboard-write; encrypted-media; fullscreen; picture-in-picture"
                        loading="lazy"
                    ></iframe>
                </article>
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
