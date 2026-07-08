    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

        @include('livewire.landing.partials.header')

        <main>
            @include('livewire.landing.partials.merch-section', ['staticLayout' => true])
        </main>

        @include('livewire.landing.partials.merch-modal')
        @include('livewire.landing.partials.footer')
    </div>
