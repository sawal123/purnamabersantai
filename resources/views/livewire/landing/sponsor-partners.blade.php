@php
    $logoUrl = fn (?string $path, string $fallback = '') => $path
        ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path))
        : $fallback;
    $heading = ($landingSectionHeadings ?? collect())->get('sponsors');
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            @include('livewire.landing.partials.section-elements', ['offset' => 8])

            <div class="relative z-10 mx-auto max-w-6xl px-5 lg:px-8">
                <div class="text-center">
                    <p class="landing-heading-kicker">
                        {{ $heading?->kicker ?: 'Sponsor & Partner' }}
                    </p>
                    <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Collaboration Opportunities'])
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                        {{ $heading?->subtitle ?: ($landingSetting?->sponsor_text ?? 'Purnama Bersantai membuka slot sponsor dan partner untuk brand activation, booth experience, serta campaign kolaboratif.') }}
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">
                @if ($sponsorPartners->isNotEmpty())
                    <div class="flex flex-wrap justify-center gap-y-6">
                        @foreach ($sponsorPartners as $partner)
                            <div class="w-1/2 px-2 sm:w-1/3 lg:w-1/4 xl:w-1/5">
                                <div
                                    class="flex h-24 items-center justify-center rounded-[1.25rem] border border-white bg-white p-2 transition duration-300 hover:-translate-y-1 hover:border-[#fff700] hover:bg-[#fff700]"
                                    aria-label="{{ $partner->name }}"
                                >
                                    @if ($partner->logo_path)
                                        <img
                                            src="{{ $logoUrl($partner->logo_path) }}"
                                            alt="{{ $partner->name }}"
                                            class="max-h-20 w-auto max-w-[92%] object-contain"
                                        />
                                    @else
                                        <span class="text-center text-sm font-semibold uppercase tracking-[0.18em] text-[#2f2e2e]">
                                            {{ $partner->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="mx-auto max-w-4xl rounded-[1.75rem] border border-white/10 bg-white/5 px-6 py-12 text-center shadow-[0_18px_48px_rgba(0,0,0,0.24)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]/80">
                            Open Collaboration
                        </p>
                        <h2 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                            Become Our Partner
                        </h2>
                        <p class="mx-auto mt-3 max-w-2xl text-lg leading-relaxed text-white/70">
                            Saat ini daftar sponsor dan partner belum ditampilkan. Hubungi tim kami untuk membuka kolaborasi baru.
                        </p>
                    </div>
                @endif

                <div class="mt-10 text-center">
                    <a href="{{ route('landing.contact') }}" class="inline-flex min-w-[12rem] items-center justify-center rounded-2xl border border-white/12 bg-[#2f2e2e] px-6 py-3 font-display text-2xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-[#242323] disabled:cursor-not-allowed disabled:opacity-60" wire:navigate>
                        Partner With Us
                    </a>
                </div>
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
