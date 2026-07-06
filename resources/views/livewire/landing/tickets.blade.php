    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

        @include('livewire.landing.partials.header')

        <main>
            @include('livewire.landing.partials.tickets-section', ['staticLayout' => true])

            <section class="relative z-10 pb-24">
                <div class="mx-auto max-w-5xl px-5 text-center lg:px-8">
                    <div class="reveal rounded-[1.75rem] border border-[#ff9f3c]/30 bg-[#2a0c14]/65 px-6 py-8 shadow-[0_18px_48px_rgba(0,0,0,0.28)]">
                        <p class="text-sm font-semibold uppercase tracking-[0.24em] text-[#ffbf6c]">
                            Official Purchase Flow
                        </p>
                        <h2 class="mt-4 font-display text-5xl uppercase tracking-[0.08em] text-white sm:text-6xl">
                            Need Help Before Checkout?
                        </h2>
                        <p class="mx-auto mt-3 max-w-2xl text-lg leading-relaxed text-white/70">
                            Gunakan halaman contact untuk menaruh arahan pembelian resmi, FAQ singkat, atau tombol menuju WhatsApp/admin ticketing.
                        </p>
                        <div class="mt-8 flex flex-col justify-center gap-4 sm:flex-row">
                            <a href="{{ route('landing.contact') }}" class="rounded-2xl bg-ember px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-white transition hover:-translate-y-1 hover:bg-red-500" wire:navigate>Contact Ticketing</a>
                            <a href="{{ route('landing.about') }}" class="rounded-2xl border-2 border-[#ff9f3c] px-7 py-3 font-display text-3xl uppercase tracking-[0.08em] text-[#ffbf6c] transition hover:-translate-y-1 hover:bg-[#ff9f3c] hover:text-[#151515]" wire:navigate>About Festival</a>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        @include('livewire.landing.partials.footer')
    </div>
