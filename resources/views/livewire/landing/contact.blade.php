<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="reveal text-center">
                    <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
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
                    @php($displayValue = $channel->value ?: $channel->url)

                    <a href="{{ $channel->url ?: route('home') }}" class="reveal group rounded-[1.5rem] border border-white/10 bg-[#2f2e2e] p-6 transition hover:-translate-y-1 hover:border-[#fff700]/70 hover:bg-[#282727]">
                        <span class="flex size-14 items-center justify-center rounded-2xl bg-[#fff700] text-[#2f2e2e]">
                            <x-contact-channel-icon :icon="$channel->icon" :type="$channel->type" class="size-7" />
                        </span>
                        <h2 class="mt-6 font-display text-4xl uppercase tracking-[0.08em] text-white">{{ $channel->label }}</h2>
                        <p class="mt-3 break-words text-lg font-semibold text-white/82">{{ $displayValue }}</p>
                    </a>
                @empty
                    <article class="reveal rounded-[1.5rem] border border-white/10 bg-[#2f2e2e] p-6">
                        <span class="flex size-14 items-center justify-center rounded-2xl bg-[#fff700] text-[#2f2e2e]">
                            <x-contact-channel-icon class="size-7" />
                        </span>
                        <h2 class="mt-6 font-display text-4xl uppercase tracking-[0.08em] text-white">Coming Soon</h2>
                        <p class="mt-3 text-lg font-semibold text-white/82">Kanal kontak resmi akan segera ditampilkan.</p>
                    </article>
                @endforelse
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
