@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => null,
    'error' => null,
])

<label class="grid gap-2">
    @if ($label)
        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $label }}</span>
    @endif

    <input
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500',
        ]) }}
    />

    @if ($error)
        @error($error)
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    @endif
</label>
