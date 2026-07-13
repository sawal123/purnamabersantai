<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Landing Audio</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Songs</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    Kelola lagu yang dipakai oleh music player di landing page. Lagu aktif pertama akan diputar di widget landing.
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
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari lagu..." error="search"
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
            Tambah Lagu
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Song'],
        ['label' => 'Audio'],
        ['label' => 'Duration'],
        ['label' => 'Order'],
        ['label' => 'Active'],
    ]">
        @forelse ($songs as $song)
            <tr wire:key="song-{{ $song->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $song->title }}</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $song->artist ?: 'No artist' }}</p>
                    <p class="mt-1 break-all text-xs text-slate-400">{{ $song->audio_path }}</p>
                </td>
                <td class="min-w-80 px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    @if ($this->audioUrl($song->audio_path))
                        <audio controls preload="metadata" class="w-full">
                            <source src="{{ $this->audioUrl($song->audio_path) }}">
                        </audio>
                    @else
                        <span class="text-zinc-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $song->duration_label ?: '-' }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $song->sort_order }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $song->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $song->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $song->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $song->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada lagu.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($songs->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $songs->firstItem() }}-{{ $songs->lastItem() }} dari {{ $songs->total() }} lagu
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($songs->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $songs->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $songs->count() }} lagu
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit Lagu' : 'Tambah Lagu'"
        description="Upload atau edit audio yang tampil di music player landing." closeAction="closeFormModal" maxWidth="max-w-3xl"
        wire:key="song-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Title" name="title" error="form.title" wire:model="form.title" />
            <x-ui-dashboard.text-input label="Artist" name="artist" error="form.artist" wire:model="form.artist" />
            <x-ui-dashboard.text-input label="Duration Label" name="duration_label" placeholder="Contoh: 03:42"
                error="form.duration_label" wire:model="form.duration_label" />
            <x-ui-dashboard.text-input label="Sort Order" name="sort_order" type="number" error="form.sort_order"
                wire:model="form.sort_order" />

            <div class="md:col-span-2 space-y-3">
                <div>
                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100">Audio File</p>
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                        Upload MP3, WAV, OGG, atau M4A maksimal 20MB. File baru akan disimpan ke folder <span class="font-mono">public/song</span>.
                    </p>
                </div>

                @if ($this->audioUrl($form['audio_path'] ?? null))
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-slate-700 dark:bg-slate-900/60">
                        <p class="mb-3 break-all text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $form['audio_path'] }}</p>
                        <audio controls preload="metadata" class="w-full">
                            <source src="{{ $this->audioUrl($form['audio_path']) }}">
                        </audio>
                    </div>
                @endif

                <label class="group relative flex min-h-36 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-indigo-500 dark:hover:bg-indigo-500/5">
                    <div class="mb-3 grid size-12 place-items-center rounded-2xl bg-white text-indigo-600 shadow-lg transition group-hover:-translate-y-1 dark:bg-slate-900 dark:text-indigo-400">
                        <x-ui-dashboard.icon name="upload" class="size-6" />
                    </div>
                    <span class="text-sm font-extrabold text-slate-800 dark:text-slate-100">
                        {{ $audioUpload ? $audioUpload->getClientOriginalName() : 'Pilih file audio' }}
                    </span>
                    <span class="mt-1 text-xs text-slate-500 dark:text-slate-400">MP3, WAV, OGG, M4A</span>

                    <input name="audioUpload" type="file" accept="audio/mpeg,audio/mp3,audio/wav,audio/ogg,audio/mp4,.mp3,.wav,.ogg,.m4a"
                        class="absolute inset-0 h-full w-full cursor-pointer opacity-0" wire:model="audioUpload">
                </label>

                @error('audioUpload')
                    <span class="block text-sm font-semibold text-rose-500">{{ $message }}</span>
                @enderror
            </div>

            <div class="md:col-span-2">
                <x-ui-dashboard.checkbox label="Active" error="form.is_active" wire:model="form.is_active" />
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
        title="Hapus lagu ini?"
        description="Record lagu akan dihapus dari database. File audio di folder public/song tidak ikut dihapus otomatis."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
