@php
    $marquee = ($landingMarquees ?? collect())->get('lineup_ticket');
    $primaryText = $marquee?->primary_text ?: 'Get Your Ticket';
    $secondaryText = $marquee?->secondary_text ?: 'Official Event Pass';
    $repeatCount = max(1, (int) ($marquee?->repeat_count ?: 10));
@endphp

<div class="lineup-ticket-marquee reveal" aria-label="{{ $marquee?->aria_label ?: 'Official event ticket marquee' }}">
    <div class="lineup-ticket-marquee-track" aria-hidden="true">
        @for ($i = 0; $i < $repeatCount; $i++)
            <span>{{ $primaryText }}</span>
            @if (filled($secondaryText))
                <span class="{{ ($marquee?->highlight_secondary ?? true) ? 'text-[#fff700]' : '!text-white' }}">{{ $secondaryText }}</span>
            @endif
        @endfor
    </div>
</div>
