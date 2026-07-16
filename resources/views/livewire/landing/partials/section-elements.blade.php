@props([
    'offset' => 0,
    'variant' => null,
])

@php
    $elements = collect(glob(public_path('landing/assets/element/*.{png,jpg,jpeg,webp,svg}'), GLOB_BRACE) ?: [])
        ->sort()
        ->map(fn (string $path) => asset('landing/assets/element/'.rawurlencode(basename($path))))
        ->values();

    $pickElement = function (int $index) use ($elements, $offset): ?string {
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
                    src="{{ $element }}"
                    alt=""
                    class="landing-section-element landing-section-element-{{ $index + 1 }}"
                    loading="lazy"
                >
            @endif
        @endforeach
    </div>
@endif
