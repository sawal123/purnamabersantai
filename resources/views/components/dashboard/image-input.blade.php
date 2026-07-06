@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'current' => null,
    'preview' => null,
])

<div class="grid gap-2">
    @if ($label)
        <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $label }}</span>
    @endif

    <label class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-5 text-center transition hover:border-amber-400 hover:bg-amber-50 dark:border-zinc-700 dark:bg-zinc-950/60 dark:hover:border-amber-400 dark:hover:bg-amber-400/10">
        @if ($preview)
            <img src="{{ $preview }}" alt="Preview {{ $label }}" class="mb-4 h-36 w-full rounded-xl object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
        @elseif ($current)
            <img src="{{ $current }}" alt="Current {{ $label }}" class="mb-4 h-36 w-full rounded-xl object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
        @else
            <div class="mb-4 flex size-14 items-center justify-center rounded-full bg-white text-zinc-500 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-700">
                <flux:icon.photo class="size-7" />
            </div>
        @endif

        <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Upload gambar</span>
        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG, JPEG, atau WEBP maksimal 4MB</span>

        <input
            name="{{ $name }}"
            type="file"
            accept="image/png,image/jpeg,image/jpg,image/webp"
            {{ $attributes->merge(['class' => 'sr-only']) }}
        >
    </label>

    @if ($current)
        <span class="break-all text-xs text-zinc-500 dark:text-zinc-400">{{ $current }}</span>
    @endif

    @if ($error)
        @error($error)
            <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
    @endif
</div>
