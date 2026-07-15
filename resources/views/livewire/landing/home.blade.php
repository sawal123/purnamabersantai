<div class="relative overflow-x-hidden">
    <div class="pointer-events-none absolute inset-x-0 top-0 h-[38rem] bg-haze"></div>

    @include('livewire.landing.partials.header')

    <main>
        @include('livewire.landing.partials.hero')
        @include('livewire.landing.partials.lineup-section', ['compactTop' => true])
        @include('livewire.landing.partials.about-video')
        @include('livewire.landing.partials.lineup-ticket-marquee')
        @include('livewire.landing.partials.tickets-section')
        @include('livewire.landing.partials.tickets-merch-marquee')
        @include('livewire.landing.partials.merch-section')
        @include('livewire.landing.partials.gallery-marquee')
        @include('livewire.landing.partials.gallery-section')
        <livewire:landing.share-moment-form />
    </main>

    @include('livewire.landing.partials.merch-modal')
    @include('livewire.landing.partials.footer')
</div>
