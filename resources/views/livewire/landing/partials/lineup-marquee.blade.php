@php
    $marquee = ($landingMarquees ?? collect())->get('lineup');
    $primaryText = $marquee?->primary_text ?: 'PURNAMA BERSANTAI 2026';
    $secondaryText = $marquee?->secondary_text;
    $repeatCount = max(1, (int) ($marquee?->repeat_count ?: 8));
@endphp

<div class="lineup-marquee" aria-label="{{ $marquee?->aria_label ?: 'Purnama Bersantai 2026' }}">
    <div class="lineup-marquee-track" aria-hidden="true">
        @for ($i = 0; $i < $repeatCount; $i++)
            <span>{{ $primaryText }}</span>
            @if (filled($secondaryText))
                <span class="{{ $marquee?->highlight_secondary ? 'text-[#fff700]' : '' }}">{{ $secondaryText }}</span>
            @endif
        @endfor
    </div>
</div>
