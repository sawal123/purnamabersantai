@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $heading = ($landingSectionHeadings ?? collect())->get('rundown_map');
    $categories = collect($rundownMapCategories ?? [])->filter(fn ($category) => $category->rundownMaps->isNotEmpty())->values();
    $firstCategory = $categories->first();
    $notFoundImage = ($notFoundImages ?? collect())->get('rundown-map');
@endphp

<div
    class="relative overflow-x-hidden"
    x-data="{ tab: @js($firstCategory?->slug) }"
>
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main class="relative z-10 pb-24 pt-32">
        <section class="mx-auto max-w-7xl px-5 lg:px-8">
            <div class="mx-auto max-w-4xl text-center">
                <p class="landing-heading-kicker">
                    {{ $heading?->kicker ?: 'Event Guide' }}
                </p>
                <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                    @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Rundown & Map'])
                </h1>
                <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                    {{ $heading?->subtitle ?: 'Lihat susunan acara, area festival, dan lokasi resmi Purnama Bersantai.' }}
                </p>
            </div>

            @if ($categories->isNotEmpty())
                <div class="mx-auto mt-10 flex w-fit flex-wrap justify-center gap-2 rounded-2xl border border-white/12 bg-[#2f2e2e]/80 p-1">
                    @foreach ($categories as $category)
                        <button
                            type="button"
                            class="rounded-xl px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] transition"
                            :class="tab === @js($category->slug) ? 'bg-[#fff700] text-[#2f2e2e]' : 'text-white/70 hover:text-white'"
                            x-on:click="tab = @js($category->slug)"
                        >
                            {{ $category->name }}
                        </button>
                    @endforeach
                </div>

                <div class="mx-auto mt-12 max-w-6xl space-y-10">
                    @foreach ($categories as $category)
                        <section
                            x-cloak
                            x-show="tab === @js($category->slug)"
                            x-transition.opacity.duration.200ms
                            class="space-y-8"
                            wire:key="rundown-map-category-{{ $category->id }}"
                        >
                            @foreach ($category->rundownMaps as $item)
                                <img
                                    src="{{ $imageUrl($item->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                                    alt="{{ $item->title ?: $category->name }}"
                                    class="block h-auto w-full rounded-[1.5rem] border border-white/10 bg-[#2f2e2e]/50"
                                    loading="lazy"
                                    wire:key="rundown-map-image-{{ $item->id }}"
                                >
                            @endforeach
                        </section>
                    @endforeach
                </div>
            @else
                @if ($notFoundImage)
                    <img
                        src="{{ $imageUrl($notFoundImage->image_path, asset('landing/assets/Rectangle 17.png')) }}"
                        alt="{{ $notFoundImage->title }}"
                        class="mx-auto mt-12 block h-auto w-full max-w-6xl"
                    >
                @else
                    <div class="mx-auto mt-12 max-w-3xl rounded-[2rem] border border-white/10 bg-[#2f2e2e]/70 px-6 py-14 text-center shadow-[0_18px_48px_rgba(47,46,46,0.26)]">
                        <div class="mx-auto grid size-20 place-items-center rounded-full border border-[#fff700]/25 bg-[#fff700]/10">
                            <span class="font-display text-5xl text-[#fff700]">{{ now()->format('y') }}</span>
                        </div>
                        <h2 class="mt-5 font-display text-4xl uppercase tracking-[0.08em] text-white sm:text-5xl">
                            Rundown Sedang Dibuat
                        </h2>
                        <p class="mx-auto mt-3 max-w-2xl text-base leading-relaxed text-white/70">
                            Rundown dan map Purnama Bersantai belum tersedia. Tim kami sedang menyiapkan panduan acara terbaik.
                        </p>
                    </div>
                @endif
            @endif
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
