@props([
    'label' => null,
    'name' => null,
    'placeholder' => 'Tulis konten...',
    'error' => null,
])

@php
    $wireModel = $attributes->wire('model');
@endphp

<div
    class="space-y-2"
    x-data="{
        value: @entangle($wireModel),
        command(action, value = null) {
            this.$refs.editor.focus();
            document.execCommand(action, false, value);
            this.value = this.$refs.editor.innerHTML;
        },
        setLink() {
            const url = window.prompt('Masukkan URL');
            if (url) {
                this.command('createLink', url);
            }
        },
    }"
    x-init="$refs.editor.innerHTML = value || ''"
    x-effect="if (($refs.editor.innerHTML || '') !== (value || '') && document.activeElement !== $refs.editor) $refs.editor.innerHTML = value || ''"
>
    @if ($label)
        <span class="block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $label }}</span>
    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-800">
        <div class="flex flex-wrap gap-2 border-b border-slate-200 bg-slate-50 p-2 dark:border-slate-700 dark:bg-slate-900/60">
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-extrabold text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('bold')">B</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-extrabold italic text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('italic')">I</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-extrabold underline text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('underline')">U</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('insertUnorderedList')">List</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('insertOrderedList')">1. List</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="setLink()">Link</button>
            <button type="button" class="rounded-xl px-3 py-2 text-sm font-bold text-slate-700 transition hover:bg-white dark:text-slate-200 dark:hover:bg-slate-800" x-on:click="command('removeFormat')">Clear</button>
        </div>

        <div
            x-ref="editor"
            contenteditable="true"
            role="textbox"
            aria-multiline="true"
            data-placeholder="{{ $placeholder }}"
            class="dashboard-rich-editor min-h-56 w-full px-4 py-3 text-sm leading-7 text-slate-950 outline-none focus:ring-4 focus:ring-indigo-100 dark:text-white dark:focus:ring-indigo-500/10"
            x-on:input="value = $refs.editor.innerHTML"
            x-on:blur="value = $refs.editor.innerHTML"
        ></div>
    </div>

    @if ($error)
        @error($error)
            <span class="block text-sm font-semibold text-rose-500">{{ $message }}</span>
        @enderror
    @endif
</div>
