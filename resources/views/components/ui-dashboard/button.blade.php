@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'navigate' => false,
])

@php
    $variants = [
        'primary' => 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-700 focus:ring-indigo-200 dark:focus:ring-indigo-500/20',
        'secondary' => 'bg-slate-900 text-white hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-100',
        'ghost' => 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 focus:ring-rose-200 dark:focus:ring-rose-500/20',
    ];

    $sizes = [
        'sm' => 'px-3 py-2 text-sm rounded-xl',
        'md' => 'px-5 py-2.5 text-sm rounded-xl',
    ];

    $classes = 'inline-flex items-center justify-center gap-2 font-bold transition hover:-translate-y-0.5 focus:outline-none focus:ring-4 disabled:cursor-not-allowed disabled:translate-y-0 disabled:opacity-60 '.$variants[$variant].' '.$sizes[$size];
@endphp

@if ($href)
    @if ($navigate)
        <a href="{{ $href }}" wire:navigate {{ $attributes->class($classes) }}>
            {{ $slot }}
        </a>
    @else
        <a href="{{ $href }}" {{ $attributes->class($classes) }}>
            {{ $slot }}
        </a>
    @endif
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
