@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;

    $mapSource = static function (?string $map): ?string {
        if (! is_string($map) || trim($map) === '') {
            return null;
        }

        $map = trim(html_entity_decode($map));

        if (preg_match('/src=["\']([^"\']+)["\']/i', $map, $matches) === 1) {
            return $matches[1];
        }

        return $map;
    };
@endphp

<div
    class="relative overflow-x-hidden"
    x-data="{ tab: 'rundown', selectedRundown: null }"
    x-effect="document.body.classList.toggle('modal-open', selectedRundown !== null)"
    x-on:keydown.escape.window="selectedRundown = null"
>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main class="relative z-10 pb-24 pt-32">
        <section class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                    Event Guide
                </p>
                <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                    Rundown & Map
                </h1>
                <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                    Lihat susunan acara, area festival, dan lokasi resmi Purnama Bersantai.
                </p>
            </div>

            <div class="mx-auto mt-10 flex w-fit rounded-2xl border border-white/12 bg-[#2f2e2e]/80 p-1">
                <button
                    type="button"
                    class="rounded-xl px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] transition"
                    :class="tab === 'rundown' ? 'bg-[#fff700] text-[#2f2e2e]' : 'text-white/70 hover:text-white'"
                    x-on:click="tab = 'rundown'"
                >
                    Rundown
                </button>
                <button
                    type="button"
                    class="rounded-xl px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] transition"
                    :class="tab === 'map' ? 'bg-[#fff700] text-[#2f2e2e]' : 'text-white/70 hover:text-white'"
                    x-on:click="tab = 'map'"
                >
                    Map
                </button>
            </div>

            <div class="mt-12">
                @if ($rundownMaps->isNotEmpty())
                    <section x-show="tab === 'rundown'" x-transition.opacity.duration.200ms>
                        @php($hasRundownImages = $rundownMaps->flatMap->images->isNotEmpty())

                        @if ($hasRundownImages)
                            <div class="flex flex-wrap justify-center gap-y-5">
                                @foreach ($rundownMaps as $rundown)
                                    @foreach ($rundown->images as $image)
                                        <div class="w-1/2 px-2.5 lg:w-1/4" wire:key="rundown-image-{{ $image->id }}">
                                            <button
                                                type="button"
                                                class="lineup-card rundown-lineup-card relative isolate aspect-[4/5] w-full cursor-zoom-in overflow-hidden rounded-[1.75rem]"
                                                aria-label="Open full image for {{ $image->name }}"
                                                data-rundown-src="{{ $imageUrl($image->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                                                data-rundown-alt="{{ $image->name ?: 'Rundown image' }}"
                                                data-rundown-name="{{ $image->name }}"
                                                data-rundown-year="{{ $rundown->tahun }}"
                                                x-on:click="selectedRundown = {
                                                    src: $el.dataset.rundownSrc,
                                                    alt: $el.dataset.rundownAlt,
                                                    name: $el.dataset.rundownName,
                                                    year: $el.dataset.rundownYear
                                                }"
                                            >
                                                <div class="lineup-media">
                                                    <img
                                                        src="{{ $imageUrl($image->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                                                        alt="{{ $image->name ?: 'Rundown image' }}"
                                                        class="object-cover"
                                                    />
                                                </div>
                                                <div class="glass-name rounded-2xl px-3 py-2 text-center">
                                                    <p class="font-display text-[1.35rem] uppercase tracking-[0.08em] text-white sm:text-[1.55rem]">
                                                        {{ $image->name }}
                                                    </p>
                                                    <p class="mt-0.5 text-xs font-semibold uppercase tracking-[0.18em] text-white/60">
                                                        {{ $rundown->tahun }}
                                                    </p>
                                                </div>
                                            </button>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-[2rem] border border-white/10 bg-[#2f2e2e]/70 px-6 py-14 text-center">
                                <h2 class="font-display text-4xl uppercase tracking-[0.08em] text-white">
                                    Rundown Coming Soon
                                </h2>
                                <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/70">
                                    Detail rundown festival akan segera ditampilkan.
                                </p>
                            </div>
                        @endif
                    </section>

                    <section x-cloak x-show="tab === 'map'" x-transition.opacity.duration.200ms>
                        @php($hasMaps = $rundownMaps->contains(fn ($rundown) => $mapSource($rundown->google_map) !== null))

                        @if ($hasMaps)
                            <div class="grid gap-6">
                                @foreach ($rundownMaps as $rundown)
                                    @php($mapSrc = $mapSource($rundown->google_map))

                                    @if ($mapSrc)
                                        <article wire:key="rundown-map-frame-{{ $rundown->id }}" class="overflow-hidden rounded-[1.75rem] border border-white/12 bg-[#2f2e2e]/72">
                                            <div class="flex items-center justify-between border-b border-white/10 px-5 py-4">
                                                <h2 class="font-display text-3xl uppercase tracking-[0.08em] text-white">
                                                    Festival Map
                                                </h2>
                                                <span class="text-sm font-semibold uppercase tracking-[0.18em] text-[#fff700]/80">
                                                    {{ $rundown->tahun }}
                                                </span>
                                            </div>
                                            <iframe
                                                src="{{ $mapSrc }}"
                                                class="h-[26rem] w-full"
                                                loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"
                                                allowfullscreen
                                            ></iframe>
                                        </article>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-[2rem] border border-white/10 bg-[#2f2e2e]/70 px-6 py-14 text-center">
                                <h2 class="font-display text-4xl uppercase tracking-[0.08em] text-white">
                                    Map Coming Soon
                                </h2>
                                <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/70">
                                    Lokasi festival akan segera ditampilkan.
                                </p>
                            </div>
                        @endif
                    </section>
                @else
                    <div class="rounded-[2rem] border border-white/10 bg-[#2f2e2e]/70 px-6 py-14 text-center">
                        <h2 class="font-display text-4xl uppercase tracking-[0.08em] text-white">
                            Rundown & Map Coming Soon
                        </h2>
                        <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/70">
                            Detail rundown dan map festival akan segera ditampilkan.
                        </p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    <div
        x-cloak
        x-show="selectedRundown"
        x-transition.opacity.duration.200ms
        class="fixed inset-0 z-[80] grid place-items-center p-4"
        aria-modal="true"
        role="dialog"
        aria-labelledby="rundown-detail-title"
    >
        <button
            type="button"
            class="absolute inset-0 cursor-zoom-out bg-black/75 backdrop-blur-md"
            aria-label="Close rundown image"
            x-on:click="selectedRundown = null"
        ></button>

        <div
            x-show="selectedRundown"
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
                aria-label="Close rundown image"
                x-on:click="selectedRundown = null"
            >
                <span aria-hidden="true">&times;</span>
            </button>

            <div class="grid min-h-0 bg-black/20">
                <img
                    x-bind:src="selectedRundown?.src"
                    x-bind:alt="selectedRundown?.alt"
                    class="block max-h-[82dvh] max-w-[94vw] object-contain"
                />
            </div>

            <div class="border-t border-white/10 px-5 py-3 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-[#fff700]/80" x-text="selectedRundown?.year"></p>
                <h2 id="rundown-detail-title" class="mt-1 font-display text-3xl uppercase tracking-[0.08em] text-white sm:text-4xl" x-text="selectedRundown?.name"></h2>
            </div>
        </div>
    </div>

    @include('livewire.landing.partials.footer')
</div>
