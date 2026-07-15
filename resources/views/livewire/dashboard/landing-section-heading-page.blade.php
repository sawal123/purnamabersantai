@once
    <style>
        @font-face {
            font-family: "Landing Slackey Preview";
            src: url("/landing/fonts/Slackey-Regular.ttf") format("truetype");
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }

        @font-face {
            font-family: "Landing Barlow Preview";
            src: url("/landing/fonts/Barlow-Medium.ttf") format("truetype");
            font-weight: 500;
            font-style: normal;
            font-display: swap;
        }

        .landing-section-heading-preview {
            font-family: "Landing Barlow Preview", ui-sans-serif, system-ui, sans-serif;
        }

        .landing-section-heading-preview .font-display {
            font-family: "Landing Slackey Preview", ui-serif, Georgia, serif !important;
            font-weight: 400 !important;
        }
    </style>
@endonce

<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-orange-700 to-amber-500 p-6 text-white shadow-xl shadow-orange-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Landing Copy</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Section Headings</h1>
                <p class="mt-3 text-sm leading-6 text-orange-50 md:text-base">
                    Kelola kicker, title, highlight, after text, dan subtitle setiap halaman/section landing.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-50">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-50">Active</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari heading..." error="search"
                wire:model.live.debounce.300ms="search" />

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Rows per page</span>
                <select name="per_page" wire:model.live="perPage"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-500/10">
                    <option value="">Pilih jumlah data</option>
                    @foreach ($this->perPageChoices as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>

                @error('perPage')
                    <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
                @enderror
            </label>
        </div>

        <x-ui-dashboard.button wire:click="create">
            Tambah Heading
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Placement'],
        ['label' => 'Preview'],
        ['label' => 'Order'],
        ['label' => 'Active'],
    ]">
        @forelse ($headings as $heading)
            <tr wire:key="section-heading-{{ $heading->getKey() }}" class="align-top">
                @php
                    $usesTicketKicker = $heading->placement === 'tickets';
                @endphp
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $heading->label }}</p>
                    <p class="mt-1 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $heading->placement }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $heading->kicker ?: 'No kicker' }}</p>
                </td>
                <td class="min-w-96 px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <div class="landing-section-heading-preview rounded-3xl bg-[#ff4a20] p-5 text-center text-white">
                        @if (filled($heading->kicker))
                            <p class="mx-auto mb-3 inline-flex rounded-full border border-[#2f2e2e]/25 bg-[#2f2e2e]/90 px-4 py-1 font-display text-sm font-extrabold uppercase leading-none tracking-[0.22em] text-[#fff700] shadow-[0_12px_28px_rgba(0,0,0,0.16)]">
                                {{ $heading->kicker }}
                            </p>
                        @endif
                        <div class="font-display text-4xl uppercase tracking-[0.1em]">
                            <span>{{ $heading->title }}</span>
                            @if (filled($heading->highlight_text))
                                <span class="{{ $usesTicketKicker ? 'mx-2 inline-flex rounded-xl border border-white/20 bg-white/15 px-4 py-1 text-[#2f2e2e]' : 'mx-[0.18em] inline-flex items-center justify-center rounded-[0.22em] border border-white/25 bg-white/15 px-[0.28em] pb-[0.08em] pt-[0.04em] text-[#2f2e2e] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.12),0_0.24em_0.72em_rgba(47,46,46,0.12)]' }}">{{ $heading->highlight_text }}</span>
                            @endif
                            @if (filled($heading->after_highlight_text))
                                <span>{{ $heading->after_highlight_text }}</span>
                            @endif
                        </div>
                        @if (filled($heading->subtitle))
                            <p class="mx-auto mt-3 max-w-xl text-sm text-white/80">{{ $heading->subtitle }}</p>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $heading->sort_order }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $heading->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $heading->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $heading->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $heading->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada heading.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($headings->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $headings->firstItem() }}-{{ $headings->lastItem() }} dari {{ $headings->total() }} heading
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($headings->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $headings->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $headings->count() }} heading
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit Heading' : 'Tambah Heading'"
        description="Placement harus sesuai dengan section landing, contoh: tickets, merchandise, lineup, gallery." closeAction="closeFormModal" maxWidth="max-w-4xl"
        wire:key="section-heading-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Placement" name="placement" placeholder="tickets, merchandise, lineup"
                error="form.placement" wire:model="form.placement" />
            <x-ui-dashboard.text-input label="Label" name="label" error="form.label"
                wire:model.live.debounce.150ms="form.label" />

            <x-ui-dashboard.text-input label="Kicker" name="kicker" placeholder="Official Event Pass"
                error="form.kicker" wire:model.live.debounce.150ms="form.kicker" />
            <x-ui-dashboard.text-input label="Sort Order" name="sort_order" type="number" error="form.sort_order"
                wire:model="form.sort_order" />

            <x-ui-dashboard.text-input label="Title" name="title" error="form.title"
                wire:model.live.debounce.150ms="form.title" />
            <x-ui-dashboard.text-input label="Highlight Text" name="highlight_text" placeholder="Contoh: Your Ticket"
                error="form.highlight_text" wire:model.live.debounce.150ms="form.highlight_text" />

            <x-ui-dashboard.text-input label="After Highlight Text" name="after_highlight_text" placeholder="Contoh: Now"
                error="form.after_highlight_text" wire:model.live.debounce.150ms="form.after_highlight_text" />
            <div class="flex items-end">
                <x-ui-dashboard.checkbox label="Active" error="form.is_active" wire:model="form.is_active" />
            </div>

            <div class="md:col-span-2">
                <label class="block">
                    <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Subtitle</span>
                    <textarea name="subtitle" rows="3" wire:model.live.debounce.150ms="form.subtitle"
                        class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:placeholder:text-slate-500 dark:focus:ring-indigo-500/10"></textarea>
                    @error('form.subtitle')
                        <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            @php
                $formUsesTicketKicker = ($form['placement'] ?? '') === 'tickets';
            @endphp
            <div class="landing-section-heading-preview md:col-span-2 rounded-3xl bg-[#ff4a20] p-6 text-center text-white">
                @if (filled($form['kicker'] ?? ''))
                    <p class="mx-auto mb-4 inline-flex rounded-full border border-[#2f2e2e]/25 bg-[#2f2e2e]/90 px-4 py-1 font-display text-sm font-extrabold uppercase leading-none tracking-[0.22em] text-[#fff700] shadow-[0_12px_28px_rgba(0,0,0,0.16)]">
                        {{ $form['kicker'] }}
                    </p>
                @endif
                <div class="font-display text-4xl uppercase tracking-[0.1em] sm:text-5xl">
                    <span>{{ $form['title'] ?: 'Title' }}</span>
                    @if (filled($form['highlight_text'] ?? ''))
                        <span class="{{ $formUsesTicketKicker ? 'mx-2 inline-flex rounded-xl border border-white/20 bg-white/15 px-4 py-1 text-[#2f2e2e]' : 'mx-[0.18em] inline-flex items-center justify-center rounded-[0.22em] border border-white/25 bg-white/15 px-[0.28em] pb-[0.08em] pt-[0.04em] text-[#2f2e2e] shadow-[inset_0_0_0_1px_rgba(255,255,255,0.12),0_0.24em_0.72em_rgba(47,46,46,0.12)]' }}">{{ $form['highlight_text'] }}</span>
                    @endif
                    @if (filled($form['after_highlight_text'] ?? ''))
                        <span>{{ $form['after_highlight_text'] }}</span>
                    @endif
                </div>
                @if (filled($form['subtitle'] ?? ''))
                    <p class="mx-auto mt-3 max-w-xl text-sm text-white/80">{{ $form['subtitle'] }}</p>
                @endif
            </div>

            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                <x-ui-dashboard.button variant="ghost" wire:click="closeFormModal">
                    Cancel
                </x-ui-dashboard.button>
                <x-ui-dashboard.button type="submit" wire:loading.attr="disabled">
                    Save Data
                </x-ui-dashboard.button>
            </div>
        </form>
    </x-ui-dashboard.modal>

    <x-ui-dashboard.confirm-modal
        :show="$showDeleteModal"
        title="Hapus heading ini?"
        description="Section akan memakai teks fallback bawaan jika heading ini tidak tersedia atau tidak aktif."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
