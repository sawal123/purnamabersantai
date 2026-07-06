<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="reveal text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                        Official Contact
                    </p>
                    <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        Direct Visitors to the Right Channel
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                        Pilih kanal resmi untuk ticketing, merchandise, partnership, atau update festival terbaru.
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto grid max-w-6xl gap-6 px-5 md:grid-cols-2 xl:grid-cols-3 lg:px-8">
                @forelse ($contactChannels as $channel)
                    <article class="reveal rounded-[1.75rem] border border-white/10 bg-white/5 p-6 shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300/80">{{ $channel->type }}</p>
                        <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white">{{ $channel->label }}</h2>
                        <p class="mt-4 text-base leading-relaxed text-white/72">
                            {{ $channel->description ?: 'Hubungi kanal resmi Purnama Bersantai untuk informasi lebih lanjut.' }}
                        </p>
                        <p class="mt-4 text-lg font-semibold text-white">{{ $channel->value ?: $channel->url }}</p>
                        <a href="{{ $channel->url ?: route('home') }}" class="mt-8 inline-flex rounded-2xl border-2 border-ember px-6 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10">
                            Open Channel
                        </a>
                    </article>
                @empty
                    <article class="reveal rounded-[1.75rem] border border-white/10 bg-white/5 p-6 shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-amber-300/80">Contact</p>
                        <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white">Coming Soon</h2>
                        <p class="mt-4 text-base leading-relaxed text-white/72">Kanal kontak resmi akan segera ditampilkan.</p>
                    </article>
                @endforelse
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
