@props([
    'label' => null,
    'error' => null,
])

<label class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 text-sm font-bold text-slate-700 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
    <input type="checkbox" {{ $attributes->merge(['class' => 'h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600']) }}>
    <span>{{ $label }}</span>
</label>

@if ($error)
    @error($error)
        <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
    @enderror
@endif
