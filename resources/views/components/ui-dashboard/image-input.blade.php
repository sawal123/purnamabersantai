@props([
    'label' => null,
    'name' => null,
    'error' => null,
    'current' => null,
    'preview' => null,
    'maxKb' => 4096,
    'help' => null,
])

@php
    $maxKb = (int) $maxKb;
    $maxSizeLabel = $maxKb >= 1024 && $maxKb % 1024 === 0
        ? ($maxKb / 1024).'MB'
        : $maxKb.'KB';
@endphp

<div
    class="space-y-2"
    x-data="{
        defaultPreview: @js($preview ?: $current ?: ''),
        localPreview: null,
        hasImage: @js(filled($preview) || filled($current)),
        updatePreview(event) {
            const file = event.target.files?.[0];

            if (! file) {
                return;
            }

            if (this.localPreview) {
                URL.revokeObjectURL(this.localPreview);
            }

            this.localPreview = URL.createObjectURL(file);
            this.hasImage = true;
        },
        destroy() {
            if (this.localPreview) {
                URL.revokeObjectURL(this.localPreview);
            }
        },
    }"
>
    @if ($label)
        <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $label }}</span>
    @endif

    <label class="group relative flex min-h-56 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-indigo-500 dark:hover:bg-indigo-500/5">
        <img
            src="{{ $preview ?: $current ?: '' }}"
            alt="Preview {{ $label }}"
            class="absolute inset-0 h-full w-full object-cover"
            x-bind:src="localPreview || defaultPreview"
            x-show="hasImage"
        >
        <div class="absolute inset-0 bg-slate-950/45" x-show="hasImage"></div>

        <div class="relative z-10">
            <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-white text-indigo-600 shadow-lg transition group-hover:-translate-y-1 dark:bg-slate-900 dark:text-indigo-400">
                <x-ui-dashboard.icon name="upload" class="h-8 w-8" />
            </div>
            <p
                class="mt-4 font-extrabold"
                x-bind:class="hasImage ? 'text-white' : 'text-slate-900 dark:text-white'"
            >
                <span x-text="hasImage ? 'Ganti gambar' : 'Klik untuk upload gambar'"></span>
            </p>
            <p
                class="mt-1 text-sm"
                x-bind:class="hasImage ? 'text-white/80' : 'text-slate-500 dark:text-slate-400'"
            >
                PNG, JPG, JPEG, atau WEBP maksimal {{ $maxSizeLabel }}
            </p>
            @if ($help)
                <p
                    class="mx-auto mt-1 max-w-md text-xs"
                    x-bind:class="hasImage ? 'text-white/75' : 'text-slate-500 dark:text-slate-400'"
                >
                    {{ $help }}
                </p>
            @endif
            <span class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold text-white">Pilih Gambar</span>
        </div>

        <input
            name="{{ $name }}"
            type="file"
            accept="image/png,image/jpeg,image/jpg,image/webp"
            {{ $attributes->merge(['class' => 'absolute inset-0 h-full w-full cursor-pointer opacity-0']) }}
            x-on:change="updatePreview($event)"
        >
    </label>

    @if ($current)
        <span class="block break-all text-xs text-slate-500 dark:text-slate-400">{{ $current }}</span>
    @endif

    @if ($error)
        @error($error)
            <span class="block text-sm font-semibold text-rose-500">{{ $message }}</span>
        @enderror
    @endif
</div>
