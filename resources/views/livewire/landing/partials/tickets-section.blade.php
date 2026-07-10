@php
    $formatPrice = fn ($ticket) => $ticket->currency.' '.number_format($ticket->price, 0, ',', '.');
    $isInternalUrl = fn (?string $url) => is_string($url) && ($url !== '') && (str_starts_with($url, url('/')) || str_starts_with($url, '/'));
    $ticketModalData = $tickets
        ->mapWithKeys(function ($ticket) use ($formatPrice) {
            $links = collect($ticket->purchaseOptions())
                ->map(fn (array $item, int $index) => [
                    'label' => $item['label'] ?: 'Link '.($index + 1),
                    'url' => $item['url'],
                ])
                ->values()
                ->all();

            return [
                (string) $ticket->id => [
                    'title' => $ticket->name,
                    'batchLabel' => $ticket->batch_label ?: ucfirst(str_replace('_', ' ', $ticket->status)),
                    'price' => $formatPrice($ticket),
                    'links' => $links,
                ],
            ];
        })
        ->filter(fn (array $item) => count($item['links']) > 1);
@endphp

<section id="tickets" class="ticket-section relative z-10 flex min-h-screen items-center pb-24 pt-32">
    <div class="mx-auto w-full max-w-7xl px-5 lg:px-8">
        <div class="ticket-section-heading reveal text-center">
            <p class="ticket-section-kicker">
                Official Event Pass
            </p>
            <h2 class="ticket-section-title font-display uppercase">
                <span>Get</span>
                <span class="ticket-section-title-pass">Your Ticket</span>
                <span>Now</span>
            </h2>
            <p class="mx-auto mt-4 max-w-2xl text-lg text-white/78">
                Pilih pass resmi Purnama Bersantai dan amankan aksesmu sebelum kuota habis.
            </p>
        </div>

        @if (($staticLayout ?? false) === true)
            <div class="mt-14 flex flex-wrap justify-center gap-y-6">
                @forelse ($tickets as $ticket)
                    @php
                        $purchaseOptions = $ticket->purchaseOptions();
                        $hasMultiplePurchaseOptions = count($purchaseOptions) > 1;
                        $primaryPurchaseUrl = $purchaseOptions[0]['url'] ?? route('landing.contact');
                    @endphp
                    <div class="w-full px-2 md:w-1/2 xl:w-1/3">
                        <article class="ticket-shell">
                            <span class="ticket-shell-notch" aria-hidden="true"></span>
                            <div class="ticket-shell-content">
                                <div class="ticket-shell-head">
                                    <p class="ticket-shell-title">{{ $ticket->name }}</p>
                                    <span class="ticket-shell-pill">{{ $ticket->batch_label ?: ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                </div>
                                <div class="ticket-shell-meta">
                                    <p class="ticket-shell-label">
                                        Starts From
                                        <span class="ticket-shell-availability">{{ $ticket->availability_label }}</span>
                                    </p>
                                    <p class="ticket-shell-price">{{ $formatPrice($ticket) }}</p>
                                </div>
                                <div class="ticket-shell-footer">
                                    @if ($ticket->status === 'sold_out')
                                        <button type="button" class="ticket-shell-action ticket-shell-action-sold cursor-not-allowed" disabled>
                                            Sold Out
                                        </button>
                                    @elseif ($hasMultiplePurchaseOptions)
                                        <button
                                            type="button"
                                            class="ticket-shell-action ticket-shell-action-buy"
                                            data-ticket-trigger="{{ $ticket->id }}"
                                        >
                                            Buy Tickets
                                        </button>
                                    @else
                                        <a
                                            href="{{ $primaryPurchaseUrl }}"
                                            class="ticket-shell-action ticket-shell-action-buy"
                                            @if ($isInternalUrl($primaryPurchaseUrl)) wire:navigate @endif
                                        >
                                            Buy Tickets
                                        </a>
                                    @endif
                                </div>
                            </div>
                            <div class="ticket-shell-perf"></div>
                        </article>
                    </div>
                @empty
                    <div class="w-full max-w-lg px-2">
                        <article class="ticket-shell">
                            <span class="ticket-shell-notch" aria-hidden="true"></span>
                            <div class="ticket-shell-content">
                                <div class="ticket-shell-head">
                                    <p class="ticket-shell-title">Ticket Coming Soon</p>
                                    <span class="ticket-shell-pill">Soon</span>
                                </div>
                                <div class="ticket-shell-meta">
                                    <p class="ticket-shell-label">
                                        Status
                                        <span class="ticket-shell-availability">Coming Soon</span>
                                    </p>
                                    <p class="ticket-shell-price">IDR 0</p>
                                </div>
                                <div class="ticket-shell-footer">
                                    <a href="{{ route('landing.contact') }}" class="ticket-shell-action ticket-shell-action-buy" wire:navigate>
                                        Contact Us
                                    </a>
                                </div>
                            </div>
                            <div class="ticket-shell-perf"></div>
                        </article>
                    </div>
                @endforelse
            </div>

            @if ($hasMoreTickets ?? false)
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
            <div class="swiper content-swiper ticket-swiper reveal mt-14" style="--delay: 80ms">
                <div class="swiper-wrapper">
                    @forelse ($tickets as $ticket)
                        @php
                            $purchaseOptions = $ticket->purchaseOptions();
                            $hasMultiplePurchaseOptions = count($purchaseOptions) > 1;
                            $primaryPurchaseUrl = $purchaseOptions[0]['url'] ?? route('landing.contact');
                        @endphp
                        <div class="swiper-slide">
                            <article class="ticket-shell">
                                <span class="ticket-shell-notch" aria-hidden="true"></span>
                                <div class="ticket-shell-content">
                                    <div class="ticket-shell-head">
                                        <p class="ticket-shell-title">{{ $ticket->name }}</p>
                                        <span class="ticket-shell-pill">{{ $ticket->batch_label ?: ucfirst(str_replace('_', ' ', $ticket->status)) }}</span>
                                    </div>
                                    <div class="ticket-shell-meta">
                                        <p class="ticket-shell-label">
                                            Starts From
                                            <span class="ticket-shell-availability">{{ $ticket->availability_label }}</span>
                                        </p>
                                        <p class="ticket-shell-price">{{ $formatPrice($ticket) }}</p>
                                    </div>
                                    <div class="ticket-shell-footer">
                                        @if ($ticket->status === 'sold_out')
                                            <button type="button" class="ticket-shell-action ticket-shell-action-sold cursor-not-allowed" disabled>
                                                Sold Out
                                            </button>
                                        @elseif ($hasMultiplePurchaseOptions)
                                            <button
                                                type="button"
                                                class="ticket-shell-action ticket-shell-action-buy"
                                                data-ticket-trigger="{{ $ticket->id }}"
                                            >
                                                Buy Tickets
                                            </button>
                                        @else
                                            <a
                                                href="{{ $primaryPurchaseUrl }}"
                                                class="ticket-shell-action ticket-shell-action-buy"
                                                @if ($isInternalUrl($primaryPurchaseUrl)) wire:navigate @endif
                                            >
                                                Buy Tickets
                                            </a>
                                        @endif
                                    </div>
                                </div>
                                <div class="ticket-shell-perf"></div>
                            </article>
                        </div>
                    @empty
                        <div class="swiper-slide">
                            <article class="ticket-shell">
                                <span class="ticket-shell-notch" aria-hidden="true"></span>
                                <div class="ticket-shell-content">
                                    <div class="ticket-shell-head">
                                        <p class="ticket-shell-title">Ticket Coming Soon</p>
                                        <span class="ticket-shell-pill">Soon</span>
                                    </div>
                                    <div class="ticket-shell-meta">
                                        <p class="ticket-shell-label">
                                            Status
                                            <span class="ticket-shell-availability">Coming Soon</span>
                                        </p>
                                        <p class="ticket-shell-price">IDR 0</p>
                                    </div>
                                    <div class="ticket-shell-footer">
                                        <a href="{{ route('landing.contact') }}" class="ticket-shell-action ticket-shell-action-buy" wire:navigate>
                                            Contact Us
                                        </a>
                                    </div>
                                </div>
                                <div class="ticket-shell-perf"></div>
                            </article>
                        </div>
                    @endforelse
                </div>
                <div class="section-slider-bar">
                    <div class="section-slider-pagination ticket-pagination"></div>
                    <div class="section-slider-nav">
                        <button type="button" class="section-slider-button ticket-prev" aria-label="Previous ticket slide">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" class="section-slider-button ticket-next" aria-label="Next ticket slide">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M9 18L15 12L9 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="reveal mt-10 text-center" style="--delay: 160ms">
                <a
                    href="{{ route('landing.ticket') }}"
                    class="inline-flex rounded-2xl border border-[#2f2e2e] bg-[#2f2e2e] px-8 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:border-[#fff700] hover:bg-[#fff700] hover:text-[#2f2e2e]"
                    wire:navigate
                >
                    See All Tickets
                </a>
            </div>
        @endif
    </div>
</section>

<script type="application/json" id="ticket-purchase-options-json">@json($ticketModalData)</script>

<div id="ticket-modal" class="ticket-modal" aria-hidden="true">
    <div class="ticket-modal-backdrop" data-ticket-close></div>
    <div
        class="ticket-modal-panel"
        role="dialog"
        aria-modal="true"
        aria-labelledby="ticket-modal-title"
    >
        <button
            type="button"
            class="section-slider-button ticket-modal-close"
            aria-label="Close ticket modal"
            data-ticket-close
        >
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
            </svg>
        </button>

        <div class="ticket-modal-shell">
            <p id="ticket-modal-batch" class="text-sm font-semibold uppercase tracking-[0.24em] text-[#fff700]"></p>
            <h3 id="ticket-modal-title" class="mt-3 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl"></h3>
            <p id="ticket-modal-price" class="mt-4 font-display text-4xl uppercase tracking-[0.05em] text-[#fff700] sm:text-5xl"></p>
            <p class="mt-3 max-w-2xl text-base text-white/68 sm:text-lg">
                Pilih channel pembelian yang ingin Anda gunakan untuk melanjutkan checkout ticket.
            </p>

            <div id="ticket-modal-links" class="ticket-modal-links mt-8"></div>
        </div>
    </div>
</div>
