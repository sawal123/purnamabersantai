@php
    $titleText = filled($heading?->title) ? $heading->title : ($fallbackTitle ?? '');
    $highlightText = filled($heading?->highlight_text) ? $heading->highlight_text : ($fallbackHighlight ?? null);
    $afterText = filled($heading?->after_highlight_text) ? $heading->after_highlight_text : ($fallbackAfter ?? null);
    $highlightClass = $highlightClass ?? 'landing-heading-highlight';
@endphp

<span>{{ $titleText }}</span>
@if (filled($highlightText))
    <span class="{{ $highlightClass }}">{{ $highlightText }}</span>
@endif
@if (filled($afterText))
    <span>{{ $afterText }}</span>
@endif
