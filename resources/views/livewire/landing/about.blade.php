    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

        @include('livewire.landing.partials.header')

        <main>
            <section class="relative z-10 pb-12 pt-32">
                <div class="mx-auto max-w-6xl px-5 lg:px-8">
                    <div class="reveal text-center">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                            About the Festival
                        </p>
                        <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                            {{ $landingSetting?->hero_tagline ?? 'Warm Nights, Loud Choruses, Shared Memories' }}
                        </h1>
                        <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                            {{ $landingSetting?->hero_description ?? 'Purnama Bersantai adalah festival musik malam yang menggabungkan performer pilihan, merchandise eksklusif, suasana komunitas, dan pengalaman event yang hangat dalam satu panggung.' }}
                        </p>
                    </div>
                </div>
            </section>

            <section class="relative z-10 pb-24">
                <div class="mx-auto max-w-6xl px-5 lg:px-8">
                    <article class="reveal rounded-[1.75rem] border border-white/10 bg-white/5 p-6 shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                            Who We Are
                        </p>
                        <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white">
                            Organisasi di Balik Purnama Bersantai
                        </h2>
                        <p class="mt-4 text-base leading-relaxed text-white/72">
                            Purnama Bersantai adalah organisasi kreatif yang berfokus pada pengembangan festival, kolaborasi komunitas, dan pengalaman hiburan yang terasa hangat, dekat, dan relevan dengan audiens muda perkotaan.
                        </p>
                        <p class="mt-4 text-base leading-relaxed text-white/72">
                            Kami membangun ruang temu antara musisi, pelaku UMKM, komunitas, sponsor, dan penonton dalam satu ekosistem acara yang tidak hanya menghadirkan hiburan, tetapi juga membuka peluang pertumbuhan bersama.
                        </p>
                    </article>
                </div>

                <div class="mx-auto mt-6 max-w-6xl px-5 lg:px-8">
                    <section class="reveal overflow-hidden rounded-[1.9rem] border border-white/10 bg-white/5 p-6 shadow-[0_18px_48px_rgba(0,0,0,0.25)] sm:p-8">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                                Our History
                            </p>
                            <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl">
                                Tumbuh Bersama Komunitas
                            </h2>
                            <p class="mt-4 text-base leading-relaxed text-white/72">
                                Perjalanan Purnama Bersantai dibangun sedikit demi sedikit, dari gathering yang hangat hingga menjadi festival yang punya identitas kuat dan ruang kolaborasi yang lebih luas.
                            </p>
                        </div>

                        <div class="relative mt-10">
                            <div class="space-y-6">
                                @foreach ($festivalHistory as $item)
                                    <article class="relative grid gap-4 md:grid-cols-[7rem_minmax(0,1fr)] md:gap-6">
                                        <div class="relative flex items-start justify-center md:justify-center">
                                            @if (! $loop->last)
                                                <span class="absolute left-1/2 top-[3.4rem] hidden h-[calc(100%+1.5rem)] w-px -translate-x-1/2 bg-gradient-to-b from-amber-300/55 via-white/18 to-transparent md:block"></span>
                                            @endif
                                            <div class="inline-flex rounded-full border border-amber-300/25 bg-amber-300/10 px-4 py-2 font-display text-2xl uppercase tracking-[0.08em] text-amber-200 md:min-w-[5.5rem] md:justify-center">
                                                {{ $item['year'] }}
                                            </div>
                                        </div>

                                        <a href="{{ route('landing.history.show', ['title' => $item['slug']]) }}" class="block rounded-[1.6rem] border border-white/10 bg-[linear-gradient(180deg,rgba(255,255,255,0.08)_0%,rgba(255,255,255,0.03)_100%)] p-5 shadow-[0_12px_34px_rgba(0,0,0,0.22)] transition hover:-translate-y-1 hover:bg-[linear-gradient(180deg,rgba(255,255,255,0.1)_0%,rgba(255,255,255,0.04)_100%)]" wire:navigate>
                                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                                <div>
                                                    <h3 class="font-display text-3xl uppercase tracking-[0.08em] text-white">
                                                        {{ $item['title'] }}
                                                    </h3>
                                                    <p class="mt-2 text-sm font-semibold uppercase tracking-[0.2em] text-amber-200/80">
                                                        {{ $item['location'] }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-white/10 bg-white/6 text-white/80">
                                                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                                                        <path d="M7 17L17 7M17 7H8.5M17 7V15.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                            </div>

                                            <p class="mt-4 text-base leading-relaxed text-white/68">
                                                {{ $item['summary'] }}
                                            </p>
                                        </a>
                                    </article>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-10 flex justify-center">
                            <a href="{{ route('landing.history') }}" class="inline-flex rounded-2xl bg-ember px-6 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-red-500" wire:navigate>
                                Lihat Semua History
                            </a>
                        </div>
                    </section>
                </div>

            </section>
        </main>

        @include('livewire.landing.partials.footer')
    </div>
