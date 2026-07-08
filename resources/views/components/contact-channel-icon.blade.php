@props([
    'icon' => null,
    'type' => null,
    'class' => 'size-8',
])

@php
    $iconName = strtolower((string) ($icon ?: $type ?: 'chat-bubble'));
    $iconName = match ($iconName) {
        'whatsapp', 'phone' => 'phone',
        'email', 'mail' => 'envelope',
        'instagram', 'camera' => 'camera',
        'tiktok', 'music', 'music-note' => 'music-note',
        'website', 'web', 'globe' => 'globe',
        default => 'chat-bubble',
    };
@endphp

@switch($iconName)
    @case('phone')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M6.6 10.8c1.4 2.8 3.8 5.2 6.6 6.6l2.2-2.2c.3-.3.8-.4 1.2-.3 1.3.4 2.6.6 4 .6.7 0 1.2.5 1.2 1.2v3.5c0 .7-.5 1.2-1.2 1.2C10.6 21.4 2.6 13.4 2.6 3.4c0-.7.5-1.2 1.2-1.2h3.5c.7 0 1.2.5 1.2 1.2 0 1.4.2 2.7.6 4 .1.4 0 .9-.3 1.2l-2.2 2.2Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break

    @case('envelope')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M4.5 6.5h15A1.5 1.5 0 0 1 21 8v8a1.5 1.5 0 0 1-1.5 1.5h-15A1.5 1.5 0 0 1 3 16V8a1.5 1.5 0 0 1 1.5-1.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
            <path d="m4 8 8 5.5L20 8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break

    @case('camera')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <rect x="4" y="4" width="16" height="16" rx="4" stroke="currentColor" stroke-width="1.8" />
            <circle cx="12" cy="12" r="3.5" stroke="currentColor" stroke-width="1.8" />
            <circle cx="17" cy="7" r="1" fill="currentColor" />
        </svg>
        @break

    @case('music-note')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M14 5v10.5a3.5 3.5 0 1 1-2-3.2V7l8-2v3l-6 1.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break

    @case('globe')
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
            <path d="M3 12h18M12 3c2.2 2.5 3.3 5.5 3.3 9S14.2 18.5 12 21c-2.2-2.5-3.3-5.5-3.3-9S9.8 5.5 12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
        @break

    @default
        <svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
            <path d="M5 6.5A3.5 3.5 0 0 1 8.5 3h7A3.5 3.5 0 0 1 19 6.5v5A3.5 3.5 0 0 1 15.5 15H11l-5 4v-4.2A3.5 3.5 0 0 1 3 11.4V6.5Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
@endswitch
