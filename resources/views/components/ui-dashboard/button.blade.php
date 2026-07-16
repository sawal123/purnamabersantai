@props([
    'href' => null,
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'navigate' => false,
    'loading' => true,
    'loadingLabel' => 'Loading',
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

    $wireClick = $attributes->wire('click')->value();
    $wireTarget = $attributes->wire('target')->value();
    $loadingTarget = $wireTarget ?: $wireClick;
    $shouldShowLoading = ! $href && $loading && ($wireClick || $type === 'submit');

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
    <button
        type="{{ $type }}"
        @if ($shouldShowLoading && ! $attributes->has('wire:loading.attr'))
            wire:loading.attr="disabled"
        @endif
        @if ($shouldShowLoading && $loadingTarget && ! $attributes->has('wire:target'))
            wire:target="{{ $loadingTarget }}"
        @endif
        {{ $attributes->class($classes) }}
    >
        @if ($shouldShowLoading)
            <span
                class="inline-flex items-center justify-center gap-2"
                @if ($loadingTarget)
                    wire:loading.remove wire:target="{{ $loadingTarget }}"
                @else
                    wire:loading.remove
                @endif
            >
                {{ $slot }}
            </span>
            <span
                class="hidden items-center justify-center"
                @if ($loadingTarget)
                    wire:loading.flex wire:target="{{ $loadingTarget }}"
                @else
                    wire:loading.flex
                @endif
            >
                <span class="h-4 w-4 shrink-0 animate-spin rounded-full border-2 border-current border-t-transparent opacity-80" aria-hidden="true"></span>
                <span class="sr-only">{{ $loadingLabel }}</span>
            </span>
        @else
            {{ $slot }}
        @endif
    </button>
@endif
