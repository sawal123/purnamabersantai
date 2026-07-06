<footer class="relative border-t border-white/10 bg-black/20 pb-8 pt-16">
    <img
        src="{{ asset('landing/assets/sobekan.svg') }}"
        alt=""
        class="footer-tear"
    />
    <div class="mx-auto max-w-7xl px-5 pt-8 lg:px-8">
        <div class="footer-card px-6 py-7 text-white/72 lg:px-8 lg:py-8">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,1.35fr)_minmax(0,0.85fr)_minmax(0,0.8fr)]">
                <section aria-labelledby="footer-about">
                    <h2
                        id="footer-about"
                        class="font-display text-4xl uppercase tracking-[0.08em] text-white"
                    >
                        {{ $landingSetting?->site_name ?? 'Purnama Bersantai Festival' }}
                    </h2>
                    <p class="mt-3 max-w-2xl text-base leading-relaxed">
                        {{ $landingSetting?->footer_description ?? 'Purnama Bersantai adalah festival musik malam yang menghadirkan penampilan artis, ticketing resmi, merchandise eksklusif, dan momen komunitas dalam satu pengalaman event yang hangat dan berkesan.' }}
                    </p>
                    <p class="mt-4 text-sm font-medium text-white/62">
                        {{ $landingSetting?->sponsor_text ?? 'Sponsor & partner slots available.' }}
                    </p>
                </section>

                <nav aria-labelledby="footer-links">
                    <h2
                        id="footer-links"
                        class="font-display text-3xl uppercase tracking-[0.08em] text-white"
                    >
                        Quick Links
                    </h2>
                    <ul class="footer-link-list mt-4 space-y-3 text-sm font-semibold uppercase tracking-[0.16em]">
                        <li><a href="{{ route('home') }}" wire:navigate>Festival Info</a></li>
                        <li><a href="{{ route('landing.lineup') }}" wire:navigate>Lineup</a></li>
                        <li><a href="{{ route('landing.tickets') }}" wire:navigate>Ticketing</a></li>
                        <li><a href="{{ route('landing.merch') }}" wire:navigate>Merchandise</a></li>
                        <li><a href="{{ route('landing.gallery') }}" wire:navigate>Gallery</a></li>
                        <li><a href="{{ route('landing.playlist') }}" wire:navigate>Playlist</a></li>
                        <li><a href="{{ route('landing.sponsors') }}" wire:navigate>Sponsor & Partner</a></li>
                        <li><a href="{{ route('landing.about') }}" wire:navigate>About</a></li>
                        <li><a href="{{ route('landing.contact') }}" wire:navigate>Contact</a></li>
                        <li><a href="{{ route('landing.faq') }}" wire:navigate>FAQ</a></li>
                    </ul>
                </nav>

                <section aria-labelledby="footer-contact">
                    <h2
                        id="footer-contact"
                        class="font-display text-3xl uppercase tracking-[0.08em] text-white"
                    >
                        Event Info
                    </h2>
                    <div class="mt-4 space-y-3 text-sm leading-relaxed text-white/70">
                        @if ($landingSetting?->event_info)
                            @foreach ($landingSetting->event_info as $label => $value)
                                <p><span class="font-semibold text-white/85">{{ str($label)->headline() }}:</span> {{ $value }}</p>
                            @endforeach
                        @else
                            <p>Official festival updates, ticket release info, and merchandise announcements are published on this page.</p>
                            <p>Browse lineup, gallery, ticketing, and merchandise sections for the latest event information.</p>
                        @endif

                        @foreach ($contactChannels->take(2) as $channel)
                            <p><span class="font-semibold text-white/85">{{ $channel->label }}:</span> {{ $channel->value ?: $channel->url }}</p>
                        @endforeach
                    </div>
                </section>
            </div>

            <div class="mt-8 flex flex-col gap-3 border-t border-white/10 pt-5 text-sm text-white/50 lg:flex-row lg:items-center lg:justify-between">
                <p>
                    &copy; {{ now()->year }} {{ $landingSetting?->site_name ?? 'Purnama Bersantai Festival' }}. Official event
                    information, tickets, lineup, and merchandise.
                </p>
                <p>Built for festival discovery, ticket purchase, and merch drop updates.</p>
            </div>
        </div>
    </div>
</footer>
