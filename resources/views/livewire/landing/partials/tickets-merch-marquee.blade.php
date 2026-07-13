@php
    $marquee = ($landingMarquees ?? collect())->get('tickets_merch');
    $primaryText = $marquee?->primary_text ?: 'Official Tickets';
    $secondaryText = $marquee?->secondary_text ?: 'Merchandise Drop';
    $repeatCount = max(1, (int) ($marquee?->repeat_count ?: 10));
@endphp

<div class="tickets-merch-marquee" aria-label="{{ $marquee?->aria_label ?: 'Official merchandise and tickets' }}">
    <div class="tickets-merch-marquee-track" aria-hidden="true">
        @for ($i = 0; $i < $repeatCount; $i++)
            <span>{{ $primaryText }}</span>
            @if (filled($secondaryText))
                <span class="{{ ($marquee?->highlight_secondary ?? true) ? 'text-[#fff700]' : '!text-white' }}">{{ $secondaryText }}</span>
            @endif
        @endfor
    </div>
</div>
