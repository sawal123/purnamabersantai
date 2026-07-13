<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Landing Motion Text</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Landing Marquees</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    Kelola 3 teks berjalan di landing page: lineup, tickets/merchandise, dan gallery moments.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Active</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['active'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari marquee..." error="search"
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
            Tambah Marquee
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Placement'],
        ['label' => 'Texts'],
        ['label' => 'Repeat'],
        ['label' => 'Active'],
    ]">
        @forelse ($marquees as $marquee)
            <tr wire:key="landing-marquee-{{ $marquee->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $marquee->label }}</p>
                    <p class="mt-1 font-mono text-xs text-slate-500 dark:text-slate-400">{{ $marquee->placement }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $marquee->aria_label ?: 'No aria label' }}</p>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <div class="max-w-xl overflow-hidden rounded-2xl bg-[#2f2e2e] px-4 py-3 font-display text-2xl uppercase leading-none tracking-[0.12em] text-white">
                        <span>{{ $marquee->primary_text }}</span>
                        @if (filled($marquee->secondary_text))
                            <span class="{{ $marquee->highlight_secondary ? 'text-[#fff700]' : '' }} ml-6">{{ $marquee->secondary_text }}</span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $marquee->repeat_count }}x</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $marquee->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $marquee->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $marquee->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $marquee->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada marquee.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($marquees->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $marquees->firstItem() }}-{{ $marquees->lastItem() }} dari {{ $marquees->total() }} marquee
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($marquees->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $marquees->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $marquees->count() }} marquee
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit Marquee' : 'Tambah Marquee'"
        description="Placement harus sesuai dengan partial landing yang memakai marquee." closeAction="closeFormModal" maxWidth="max-w-3xl"
        wire:key="landing-marquee-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Placement" name="placement" placeholder="lineup, tickets_merch, gallery"
                error="form.placement" wire:model="form.placement" />
            <x-ui-dashboard.text-input label="Label" name="label" error="form.label" wire:model="form.label" />

            <div class="md:col-span-2">
                <x-ui-dashboard.text-input label="Aria Label" name="aria_label" error="form.aria_label"
                    wire:model="form.aria_label" />
            </div>

            <x-ui-dashboard.text-input label="Primary Text" name="primary_text" error="form.primary_text"
                wire:model.live.debounce.150ms="form.primary_text" />
            <x-ui-dashboard.text-input label="Secondary Text" name="secondary_text" error="form.secondary_text"
                wire:model.live.debounce.150ms="form.secondary_text" />

            <x-ui-dashboard.text-input label="Repeat Count" name="repeat_count" type="number" error="form.repeat_count"
                wire:model="form.repeat_count" />

            <div class="space-y-3">
                <x-ui-dashboard.checkbox label="Highlight Secondary Text" error="form.highlight_secondary"
                    wire:model.live="form.highlight_secondary" />
                <x-ui-dashboard.checkbox label="Active" error="form.is_active" wire:model="form.is_active" />
            </div>

            <div class="md:col-span-2 rounded-2xl bg-[#2f2e2e] px-4 py-3 font-display text-2xl uppercase leading-none tracking-[0.12em] text-white">
                <span>{{ $form['primary_text'] ?: 'Primary Text' }}</span>
                @if (filled($form['secondary_text'] ?? ''))
                    <span class="{{ ($form['highlight_secondary'] ?? true) ? 'text-[#fff700]' : '' }} ml-6">{{ $form['secondary_text'] }}</span>
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
        title="Hapus marquee ini?"
        description="Marquee yang dihapus akan fallback ke teks default jika placement tersebut dipakai landing."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
