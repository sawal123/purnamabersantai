@props([
    'label' => null,
    'name' => null,
    'rows' => 4,
    'placeholder' => null,
    'error' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $label }}</span>
    @endif

    <textarea
        name="{{ $name }}"
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-indigo-500/10',
        ]) }}
    ></textarea>

    @if ($error)
        @error($error)
            <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
        @enderror
    @endif
</label>
