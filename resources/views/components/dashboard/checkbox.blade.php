@props([
    'label' => null,
    'error' => null,
])

<label class="flex items-center gap-3 rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm font-medium text-zinc-700 dark:border-zinc-700 dark:bg-zinc-950/60 dark:text-zinc-200">
    <input type="checkbox" {{ $attributes->merge(['class' => 'size-4 rounded border-zinc-300 text-amber-500 focus:ring-amber-400']) }}>
    <span>{{ $label }}</span>
</label>

@if ($error)
    @error($error)
        <span class="mt-2 block text-sm text-red-500">{{ $message }}</span>
    @enderror
@endif
