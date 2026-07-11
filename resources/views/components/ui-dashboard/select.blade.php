@props([
    'label' => null,
    'name' => null,
    'options' => [],
    'placeholder' => 'Pilih opsi',
    'error' => null,
])

<label class="block">
    @if ($label)
        <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $label }}</span>
    @endif

    <select
        name="{{ $name }}"
        {{ $attributes->merge([
            'class' => 'w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-500/10',
        ]) }}
    >
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $option)
            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
        @endforeach
    </select>

    @if ($error)
        @error($error)
            <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
        @enderror
    @endif
</label>
