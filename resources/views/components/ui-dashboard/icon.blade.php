@props([
    'name' => 'circle',
])

@php
    $paths = [
        'home' => '<path d="M3 12 12 3l9 9" /><path d="M5 10v10h14V10" /><path d="M9 20v-6h6v6" />',
        'magnifying-glass' => '<path d="m21 21-4.3-4.3" /><circle cx="11" cy="11" r="7" />',
        'adjustments-horizontal' => '<path d="M3 6h12M19 6h2M15 6a2 2 0 1 0 4 0 2 2 0 0 0-4 0ZM3 18h2M9 18h12M5 18a2 2 0 1 0 4 0 2 2 0 0 0-4 0ZM3 12h7M14 12h7M10 12a2 2 0 1 0 4 0 2 2 0 0 0-4 0Z" />',
        'clock' => '<circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" />',
        'map' => '<path d="m9 18-6 3V6l6-3 6 3 6-3v15l-6 3-6-3Z" /><path d="M9 3v15M15 6v15" />',
        'photo' => '<rect x="3" y="5" width="18" height="14" rx="2" /><circle cx="8.5" cy="10.5" r="1.5" /><path d="m21 15-5-5L5 21" />',
        'calendar-days' => '<rect x="3" y="4" width="18" height="18" rx="2" /><path d="M16 2v4M8 2v4M3 10h18M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" />',
        'microphone' => '<path d="M12 14a4 4 0 0 0 4-4V6a4 4 0 1 0-8 0v4a4 4 0 0 0 4 4Z" /><path d="M19 10a7 7 0 0 1-14 0M12 17v5M8 22h8" />',
        'music-note' => '<path d="M9 18V5l12-2v13" /><circle cx="6" cy="18" r="3" /><circle cx="18" cy="16" r="3" />',
        'play' => '<circle cx="12" cy="12" r="10" /><path d="m10 8 6 4-6 4V8Z" />',
        'ticket' => '<path d="M2 9a3 3 0 0 0 0 6v3a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-3a3 3 0 0 0 0-6V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v3Z" /><path d="M13 5v2M13 11v2M13 17v2" />',
        'shopping-bag' => '<path d="M6 8h12l1 13H5L6 8Z" /><path d="M9 8a3 3 0 0 1 6 0" />',
        'rectangle-stack' => '<path d="M4 6h16v12H4z" /><path d="M8 2h8M8 22h8" />',
        'list-bullet' => '<path d="M8 6h13M8 12h13M8 18h13" /><path d="M3 6h.01M3 12h.01M3 18h.01" />',
        'camera' => '<path d="M4 7h3l2-3h6l2 3h3a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2Z" /><circle cx="12" cy="13" r="4" />',
        'building-storefront' => '<path d="M4 10h16l-1-6H5l-1 6Z" /><path d="M5 10v10h14V10M9 20v-6h6v6" />',
        'chat-bubble-left-right' => '<path d="M7 8h8M7 12h5" /><path d="M21 12a8 8 0 0 1-8 8H7l-4 3v-7a8 8 0 1 1 18-4Z" />',
        'question-mark-circle' => '<circle cx="12" cy="12" r="10" /><path d="M9.5 9a2.8 2.8 0 1 1 4.8 2c-.9.8-1.8 1.3-1.8 2.5M12 17h.01" />',
        'globe-alt' => '<circle cx="12" cy="12" r="10" /><path d="M2 12h20M12 2a15 15 0 0 1 0 20M12 2a15 15 0 0 0 0 20" />',
        'cog' => '<path d="M12 15.5A3.5 3.5 0 1 0 12 8a3.5 3.5 0 0 0 0 7.5Z" /><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.83 2.83-.06-.06A1.7 1.7 0 0 0 15 19.4a1.7 1.7 0 0 0-1 .6 1.7 1.7 0 0 0-.4 1.1V21h-4v-.1A1.7 1.7 0 0 0 8 19.4a1.7 1.7 0 0 0-1.88.34l-.06.06-2.83-2.83.06-.06A1.7 1.7 0 0 0 3.6 15a1.7 1.7 0 0 0-.6-1 1.7 1.7 0 0 0-1.1-.4H2v-4h.1A1.7 1.7 0 0 0 3.6 8a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.83-2.83.06.06A1.7 1.7 0 0 0 8 3.6a1.7 1.7 0 0 0 1-.6 1.7 1.7 0 0 0 .4-1.1V2h4v.1A1.7 1.7 0 0 0 15 3.6a1.7 1.7 0 0 0 1.88-.34l.06-.06 2.83 2.83-.06.06A1.7 1.7 0 0 0 19.4 8c.16.38.37.72.6 1 .28.34.66.55 1.1.6h.1v4h-.1A1.7 1.7 0 0 0 19.4 15Z" />',
        'arrow-right' => '<path d="M5 12h14M13 5l7 7-7 7" />',
        'arrow-up' => '<path d="M12 19V5M5 12l7-7 7 7" />',
        'arrow-down' => '<path d="M12 5v14M19 12l-7 7-7-7" />',
        'arrow-up-right' => '<path d="M7 17 17 7M9 7h8v8" />',
        'bars' => '<path d="M4 6h16M4 12h16M4 18h16" />',
        'x' => '<path d="m18 6-12 12M6 6l12 12" />',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><path d="m16 17 5-5-5-5M21 12H9" />',
        'upload' => '<path d="M12 16V4M7 9l5-5 5 5" /><path d="M20 15v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-4" />',
        'trash' => '<path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5" />',
        'bell' => '<path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M10 21h4" />',
    ];
@endphp

<svg {{ $attributes->class('h-5 w-5') }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    {!! $paths[$name] ?? '<circle cx="12" cy="12" r="10" />' !!}
</svg>
