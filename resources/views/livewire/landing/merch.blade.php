    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

        @include('livewire.landing.partials.header')

        <main>
            @include('livewire.landing.partials.merch-section', ['staticLayout' => true])

            <section class="relative z-10 pb-24">
                <div class="mx-auto max-w-5xl px-5 text-center lg:px-8">
                    <div class="reveal rounded-[1.75rem] border border-amber-300/25 bg-white/5 px-6 py-8 shadow-[0_18px_48px_rgba(0,0,0,0.25)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-amber-300/80">
                            Ready to Order
                        </p>
                        <h2 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                            Continue to Contact Page
                        </h2>
                        <p class="mx-auto mt-3 max-w-2xl text-lg leading-relaxed text-white/70">
                            Semua tombol detail produk sekarang diarahkan ke halaman contact untuk memudahkan penempatan link order resmi atau admin merchandise.
                        </p>
                        <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">
                            <a href="{{ route('landing.contact') }}" class="rounded-2xl bg-ember px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-red-500" wire:navigate>Order Merch</a>
                            <a href="{{ route('landing.gallery') }}" class="rounded-2xl border-2 border-amber-300 px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-white/10" wire:navigate>View Gallery</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('livewire.landing.partials.merch-modal')
        @include('livewire.landing.partials.footer')
    </div>
