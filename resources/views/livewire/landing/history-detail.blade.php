@php
    $imageUrl = fn (?string $path, string $fallback) => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;

    $media = $history['media'] ?? [];
    $gallery = $history['gallery'] ?? [];
    $heroImage = $imageUrl($media[0] ?? ($history['thumbnail'] ?? null), asset('landing/assets/Rectangle 17.png'));
    $secondaryMedia = array_slice($media, 1);
    $galleryImages = count($gallery) > 0 ? $gallery : $secondaryMedia;
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-10 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <a href="{{ route('landing.history') }}" class="reveal inline-flex w-fit items-center gap-3 rounded-2xl border border-white/15 bg-white/5 px-5 py-3 text-sm font-semibold uppercase tracking-[0.18em] text-white/80 transition hover:-translate-y-1 hover:bg-white/10" wire:navigate>
                    <svg viewBox="0 0 24 24" fill="none" class="h-5 w-5" aria-hidden="true">
                        <path d="M19 12H5M5 12L11 6M5 12L11 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Back to History
                </a>

                <article class="reveal mt-8 overflow-hidden rounded-[1.9rem] border border-white/10 bg-[#2f2e2e]">
                    <div class="relative min-h-[34rem] overflow-hidden">
                        <img
                            src="{{ $heroImage }}"
                            alt="{{ $history['title'] }}"
                            class="absolute inset-0 h-full w-full object-cover"
                        />
                        <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(20,1,2,0.92)_0%,rgba(20,1,2,0.58)_45%,rgba(20,1,2,0.12)_100%)]"></div>
                        <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-[#2f2e2e] to-transparent p-6 sm:p-8 lg:p-10">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/85">
                                {{ $history['year'] }} Archive
                            </p>
                            <h1 class="mt-4 max-w-4xl font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                                {{ $history['title'] }}
                            </h1>
                            <p class="mt-5 max-w-3xl text-lg leading-relaxed text-white/78">
                                {{ $history['summary'] }}
                            </p>

                            <dl class="mt-7 grid max-w-4xl gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.22em] text-[#fff700]/75">Location</dt>
                                    <dd class="mt-2 text-base font-semibold text-white">{{ $history['location'] }}</dd>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.22em] text-[#fff700]/75">Event Date</dt>
                                    <dd class="mt-2 text-base font-semibold text-white">{{ $history['date_label'] ?? 'Coming Soon' }}</dd>
                                </div>
                                <div class="rounded-2xl border border-white/10 bg-black/30 p-4">
                                    <dt class="text-xs font-semibold uppercase tracking-[0.22em] text-[#fff700]/75">Capacity</dt>
                                    <dd class="mt-2 text-base font-semibold text-white">{{ $history['capacity_label'] ?? '-' }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto grid max-w-6xl gap-8 px-5 lg:grid-cols-[minmax(0,1fr)_21rem] lg:px-8">
                <div class="space-y-8">
                    <article class="reveal rounded-[1.7rem] border border-white/10 bg-[#2f2e2e] p-6 sm:p-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                            Story
                        </p>
                        <div class="mt-5 space-y-4 text-base leading-relaxed text-white/72">
                            @foreach (preg_split('/\R+/', trim($history['content'] ?? $history['summary'])) as $paragraph)
                                @if (filled($paragraph))
                                    <p>{{ $paragraph }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    @if (count($secondaryMedia) > 0)
                        <section class="reveal grid gap-4 sm:grid-cols-2">
                            @foreach ($secondaryMedia as $image)
                                <figure class="overflow-hidden rounded-[1.5rem] border border-white/10 bg-[#2f2e2e]">
                                    <img
                                        src="{{ $imageUrl($image, asset('landing/assets/Rectangle 17.png')) }}"
                                        alt="{{ $history['title'] }} media {{ $loop->iteration }}"
                                        class="h-72 w-full object-cover"
                                    />
                                </figure>
                            @endforeach
                        </section>
                    @endif

                    @if (count($galleryImages) > 0)
                        <section class="reveal">
                            <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                                Festival Gallery
                            </p>
                            <h2 class="mt-3 font-display text-4xl uppercase tracking-[0.08em] text-white">
                                Momen {{ $history['year'] }}
                            </h2>

                            <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                @foreach ($galleryImages as $image)
                                    <figure class="overflow-hidden rounded-[1.4rem] border border-white/10 bg-[#2f2e2e]">
                                        <img
                                            src="{{ $imageUrl($image, asset('landing/assets/Rectangle 17.png')) }}"
                                            alt="{{ $history['title'] }} gallery {{ $loop->iteration }}"
                                            class="h-64 w-full object-cover"
                                        />
                                    </figure>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="reveal h-fit space-y-5 lg:sticky lg:top-28">
                    <section class="rounded-[1.6rem] border border-white/10 bg-[#2f2e2e] p-5">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#fff700]/80">
                            Detail Acara
                        </p>
                        <dl class="mt-5 space-y-4">
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-white/45">Tahun</dt>
                                <dd class="mt-1 font-display text-4xl uppercase tracking-[0.08em] text-white">{{ $history['year'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-white/45">Lokasi</dt>
                                <dd class="mt-1 text-sm font-semibold text-white">{{ $history['location'] }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-semibold uppercase tracking-[0.2em] text-white/45">Tanggal</dt>
                                <dd class="mt-1 text-sm font-semibold text-white">{{ $history['date_label'] ?? 'Coming Soon' }}</dd>
                            </div>
                        </dl>
                    </section>

                    @if (count($relatedHistory ?? []) > 0)
                        <section class="rounded-[1.6rem] border border-white/10 bg-[#2f2e2e] p-5">
                            <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#fff700]/80">
                                History Lainnya
                            </p>

                            <div class="mt-5 space-y-3">
                                @foreach ($relatedHistory as $item)
                                    <a href="{{ route('landing.history.show', ['title' => $item['slug']]) }}" class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-3 rounded-2xl border border-white/10 bg-[#2f2e2e] p-2 transition hover:-translate-y-1 hover:bg-[#242323]" wire:navigate>
                                        <img
                                            src="{{ $imageUrl($item['thumbnail'] ?? null, asset('landing/assets/Rectangle 17.png')) }}"
                                            alt="{{ $item['title'] }}"
                                            class="h-20 w-full rounded-xl object-cover"
                                        />
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#fff700]/80">{{ $item['year'] }}</p>
                                            <p class="mt-1 line-clamp-2 font-display text-2xl uppercase tracking-[0.06em] text-white">{{ $item['title'] }}</p>
                                            <p class="mt-1 truncate text-xs font-semibold uppercase tracking-[0.16em] text-white/48">{{ $item['location'] }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endif
                </aside>
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
