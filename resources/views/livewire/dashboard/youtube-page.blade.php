<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-red-600 via-orange-600 to-amber-500 p-6 text-white shadow-xl shadow-orange-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Landing Video</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">YouTube Videos</h1>
                <p class="mt-3 text-sm leading-6 text-orange-50 md:text-base">
                    Kelola video YouTube di landing page. Jika tidak ada video aktif, section video beserta sobekan kertas tidak akan tampil.
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
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari video..." error="search"
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
            Tambah YouTube
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Video'],
        ['label' => 'Preview'],
        ['label' => 'Order'],
        ['label' => 'Active'],
    ]">
        @forelse ($videos as $video)
            <tr wire:key="youtube-video-{{ $video->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $video->title }}</p>
                    <p class="mt-1 break-all text-xs text-slate-500 dark:text-slate-400">{{ $video->youtube_url }}</p>
                    <p class="mt-1 text-xs text-slate-400">{{ $video->aria_label ?: 'No aria label' }}</p>
                </td>
                <td class="min-w-72 px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    @if ($video->embed_src)
                        <div class="aspect-video overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-900">
                            <iframe
                                src="{{ $video->embed_src }}"
                                title="{{ $video->title }}"
                                class="h-full w-full"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin"
                                allowfullscreen
                            ></iframe>
                        </div>
                    @else
                        <span class="text-zinc-400">URL tidak valid</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $video->sort_order }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $video->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $video->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $video->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $video->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada video YouTube.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($videos->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $videos->firstItem() }}-{{ $videos->lastItem() }} dari {{ $videos->total() }} video
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($videos->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $videos->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $videos->count() }} video
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit YouTube' : 'Tambah YouTube'"
        description="Video aktif pertama berdasarkan sort order akan tampil di landing." closeAction="closeFormModal" maxWidth="max-w-4xl"
        wire:key="youtube-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Title" name="title" error="form.title"
                wire:model.live.debounce.150ms="form.title" />
            <x-ui-dashboard.text-input label="Sort Order" name="sort_order" type="number" error="form.sort_order"
                wire:model="form.sort_order" />

            <div class="md:col-span-2">
                <x-ui-dashboard.text-input label="YouTube URL" name="youtube_url"
                    placeholder="https://www.youtube.com/watch?v=..." error="form.youtube_url"
                    wire:model.live.debounce.300ms="form.youtube_url" />
            </div>

            <div class="md:col-span-2">
                <x-ui-dashboard.text-input label="Aria Label" name="aria_label" error="form.aria_label"
                    wire:model="form.aria_label" />
            </div>

            <div class="md:col-span-2 overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 dark:border-slate-700 dark:bg-slate-900">
                @if ($this->previewEmbedSrc())
                    <iframe
                        src="{{ $this->previewEmbedSrc() }}"
                        title="{{ $form['title'] ?: 'YouTube preview' }}"
                        class="aspect-video w-full"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin"
                        allowfullscreen
                    ></iframe>
                @else
                    <div class="grid aspect-video place-items-center px-6 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
                        Masukkan URL YouTube yang valid untuk melihat preview.
                    </div>
                @endif
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
        title="Hapus video YouTube ini?"
        description="Jika tidak ada video aktif, section YouTube dan sobekan kertas di landing tidak akan tampil."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
