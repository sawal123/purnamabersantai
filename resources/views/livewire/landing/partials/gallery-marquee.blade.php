@php
    $marquee = ($landingMarquees ?? collect())->get('gallery');
    $primaryText = $marquee?->primary_text ?: 'Beautiful Moments';
    $secondaryText = $marquee?->secondary_text ?: 'Share Your Story';
    $repeatCount = max(1, (int) ($marquee?->repeat_count ?: 10));
@endphp

<div class="tickets-merch-marquee tickets-merch-marquee-reverse-tilt" aria-label="{{ $marquee?->aria_label ?: 'Beautiful festival moments' }}">
    <div class="tickets-merch-marquee-track" aria-hidden="true">
        @for ($i = 0; $i < $repeatCount; $i++)
            <span>{{ $primaryText }}</span>
            @if (filled($secondaryText))
                <span class="{{ ($marquee?->highlight_secondary ?? true) ? 'text-[#fff700]' : '!text-white' }}">{{ $secondaryText }}</span>
            @endif
        @endfor
    </div>
</div>
