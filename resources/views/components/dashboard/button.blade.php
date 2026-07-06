@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'navigate' => false,
])

@php
    $variants = [
        'primary' => 'bg-amber-400 text-zinc-950 hover:bg-amber-300',
        'secondary' => 'bg-zinc-800 text-white hover:bg-zinc-700 dark:bg-zinc-100 dark:text-zinc-950 dark:hover:bg-white',
        'ghost' => 'bg-transparent text-zinc-600 ring-1 ring-zinc-200 hover:bg-zinc-100 dark:text-zinc-300 dark:ring-zinc-700 dark:hover:bg-zinc-800',
        'danger' => 'bg-red-500 text-white hover:bg-red-400',
    ];

    $sizes = [
        'sm' => 'px-3 py-2 text-sm',
        'md' => 'px-4 py-2.5 text-sm',
    ];

    $classes = 'inline-flex items-center justify-center rounded-2xl font-medium transition disabled:cursor-not-allowed disabled:opacity-60 '.$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    <a href="{{ $href }}" @if ($navigate) wire:navigate @endif {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
