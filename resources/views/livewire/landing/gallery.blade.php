    <div class="relative overflow-x-hidden">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-[32rem] bg-haze"></div>

        @include('livewire.landing.partials.header')

        <main>
            @include('livewire.landing.partials.gallery-section', ['staticLayout' => true])

            <livewire:landing.share-moment-form />
        </main>

        @include('livewire.landing.partials.footer')
    </div>
