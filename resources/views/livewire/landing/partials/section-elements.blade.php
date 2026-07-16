@props([
    'offset' => 0,
    'variant' => null,
    'pageSection' => null,
])

@php
    $dbElements = $pageSection
        ? collect($landingBodyElements ?? collect())->get($pageSection, collect())
        : collect();

    $elements = collect($dbElements)
        ->filter(fn ($element) => filled($element->image_path ?? null))
        ->map(function ($element) {
            $path = $element->image_path;

            return [
                'url' => str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : asset($path),
                'title' => $element->title ?? '',
            ];
        })
        ->values();

    if ($elements->isEmpty() && ($landingBodyElements ?? null) === null) {
        $elements = collect(glob(public_path('landing/assets/element/*.{png,jpg,jpeg,webp,svg}'), GLOB_BRACE) ?: [])
            ->sort()
            ->map(fn (string $path) => [
                'url' => asset('landing/assets/element/'.rawurlencode(basename($path))),
                'title' => '',
            ])
            ->values();
    }

    $pickElement = function (int $index) use ($elements, $offset): ?array {
        $count = $elements->count();

        if ($count === 0) {
            return null;
        }

        return $elements[($index + (int) $offset) % $count];
    };
@endphp

@if ($elements->isNotEmpty())
    <div class="landing-section-elements {{ $variant ? 'landing-section-elements--'.$variant : '' }}" aria-hidden="true">
        @foreach (range(0, 3) as $index)
            @php($element = $pickElement($index))
            @if ($element)
                <img
                    src="{{ $element['url'] }}"
                    alt=""
                    title="{{ $element['title'] }}"
                    class="landing-section-element landing-section-element-{{ $index + 1 }}"
                    loading="lazy"
                >
            @endif
        @endforeach
    </div>
@endif
