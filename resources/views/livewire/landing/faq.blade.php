@php
    $heading = ($landingSectionHeadings ?? collect())->get('faq');
@endphp

<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[36rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        <section class="relative z-10 pb-12 pt-32">
            <div class="mx-auto max-w-6xl px-5 lg:px-8">
                <div class="text-center">
                    <p class="landing-heading-kicker">
                        {{ $heading?->kicker ?: 'Frequently Asked Questions' }}
                    </p>
                    <h1 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                        @include('livewire.landing.partials.heading-title', ['heading' => $heading, 'fallbackTitle' => 'Yang Sering Ditanyakan'])
                    </h1>
                    <p class="mx-auto mt-4 max-w-3xl text-lg leading-relaxed text-white/72">
                        {{ $heading?->subtitle ?: 'Temukan jawaban cepat seputar festival, tiket, merchandise, dan pengalaman Purnama Bersantai.' }}
                    </p>
                </div>
            </div>
        </section>

        <section class="relative z-10 pb-24">
            <div class="mx-auto max-w-7xl px-5 lg:px-8">
                @if ($faqs->isNotEmpty())
                    <div class="space-y-5 lg:hidden">
                        @foreach ($faqs as $faq)
                            @php
                                $isActive = $this->isFaqOpen($faq->id);
                            @endphp

                            <article
                                wire:key="faq-mobile-{{ $faq->id }}"
                                class="overflow-hidden rounded-[2rem] border border-white/10 bg-[#2f2e2e]/95 text-white shadow-[0_24px_70px_rgba(0,0,0,0.28)] transition hover:border-white/20"
                            >
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-between gap-5 px-6 py-5 text-left sm:px-8 sm:py-7"
                                    wire:click="toggleFaq({{ $faq->id }})"
                                    aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                >
                                    <span class="text-lg font-bold leading-snug text-white sm:text-xl">
                                        {{ $faq->question }}
                                    </span>
                                    <span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-ember/60 bg-ember/10 font-display text-4xl leading-none text-ember transition duration-300">
                                        {{ $isActive ? '-' : '+' }}
                                    </span>
                                </button>

                                <div
                                    class="grid transition-[grid-template-rows,opacity] duration-300 ease-out {{ $isActive ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0' }}"
                                    aria-hidden="{{ $isActive ? 'false' : 'true' }}"
                                >
                                    <div class="min-h-0 overflow-hidden px-6 sm:px-8">
                                        <div class="border-t border-white/10 pb-6 pt-5 sm:pb-8">
                                            <p class="whitespace-pre-line text-base leading-relaxed text-white/70 sm:text-lg">
                                                {{ $faq->answer }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @php
                        $partitionedFaqs = $faqs->values()->partition(fn ($faq, $index) => $index % 2 === 0);
                        $faqColumns = [$partitionedFaqs[0], $partitionedFaqs[1]];
                    @endphp

                    <div class="hidden gap-8 lg:grid lg:grid-cols-2 lg:items-start">
                        @foreach ($faqColumns as $columnIndex => $columnFaqs)
                            <div class="space-y-8">
                                @foreach ($columnFaqs as $faq)
                                    @php
                                        $isActive = $this->isFaqOpen($faq->id);
                                    @endphp

                                    <article
                                        wire:key="faq-desktop-{{ $columnIndex }}-{{ $faq->id }}"
                                        class="overflow-hidden rounded-[2rem] border border-white/10 bg-[#2f2e2e]/95 text-white shadow-[0_24px_70px_rgba(0,0,0,0.28)] transition hover:border-white/20"
                                    >
                                        <button
                                            type="button"
                                            class="flex w-full items-center justify-between gap-5 px-6 py-5 text-left sm:px-8 sm:py-7"
                                            wire:click="toggleFaq({{ $faq->id }})"
                                            aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                        >
                                            <span class="text-lg font-bold leading-snug text-white sm:text-xl">
                                                {{ $faq->question }}
                                            </span>
                                                <span class="flex size-10 shrink-0 items-center justify-center rounded-full border border-ember/60 bg-ember/10 font-display text-4xl leading-none text-ember transition duration-300">
                                                    {{ $isActive ? '-' : '+' }}
                                                </span>
                                            </button>

                                            <div
                                                class="grid transition-[grid-template-rows,opacity] duration-300 ease-out {{ $isActive ? 'grid-rows-[1fr] opacity-100' : 'grid-rows-[0fr] opacity-0' }}"
                                                aria-hidden="{{ $isActive ? 'false' : 'true' }}"
                                            >
                                                <div class="min-h-0 overflow-hidden px-6 sm:px-8">
                                                    <div class="border-t border-white/10 pb-6 pt-5 sm:pb-8">
                                                        <p class="whitespace-pre-line text-base leading-relaxed text-white/70 sm:text-lg">
                                                            {{ $faq->answer }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                        @endforeach
                    </div>
                @else
                    <article class="mx-auto max-w-3xl rounded-3xl border border-white/10 bg-white/5 p-8 text-center shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-[#fff700]/80">FAQ</p>
                        <h2 class="mt-4 font-display text-4xl uppercase tracking-[0.08em] text-white">Coming Soon</h2>
                        <p class="mt-4 text-base leading-relaxed text-white/72">
                            Daftar pertanyaan dan jawaban akan segera ditampilkan.
                        </p>
                    </article>
                @endif
            </div>
        </section>
    </main>

    @include('livewire.landing.partials.footer')
</div>
