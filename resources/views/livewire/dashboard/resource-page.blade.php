<section class="w-full space-y-6">
    <div class="rounded-3xl border border-zinc-200 bg-gradient-to-br from-amber-50 via-white to-orange-50 p-6 shadow-sm dark:border-zinc-700 dark:from-zinc-900 dark:via-zinc-900 dark:to-zinc-800">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-700 dark:text-amber-300">Dashboard CRUD</p>
                <h1 class="mt-2 text-3xl font-semibold tracking-tight text-zinc-950 dark:text-white">{{ $this->pageTitle }}</h1>
                <p class="mt-3 text-sm leading-6 text-zinc-600 dark:text-zinc-300">{{ $this->resourceConfig['description'] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Total</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Active</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $summary['active'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/70 bg-white/80 p-4 backdrop-blur dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-zinc-500 dark:text-zinc-400">Page</p>
                    <p class="mt-2 text-2xl font-semibold text-zinc-950 dark:text-white">{{ $records->currentPage() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-dashboard.text-input
                label="Search"
                name="search"
                placeholder="Cari data..."
                error="search"
                wire:model.live.debounce.300ms="search"
            />

            <x-dashboard.select
                label="Rows per page"
                name="per_page"
                :options="$this->perPageChoices"
                placeholder="Pilih jumlah data"
                error="perPage"
                wire:model.live="perPage"
            />
        </div>

        <x-dashboard.button wire:click="openPrimaryAction">
            {{ $this->actionLabel }}
        </x-dashboard.button>
    </div>

    <x-dashboard.table :columns="$this->resourceConfig['table_columns']">
        @forelse ($records as $record)
            <tr wire:key="{{ $this->resource }}-{{ $record->getKey() }}" class="align-top">
                @foreach ($this->resourceConfig['table_columns'] as $column)
                    <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                        @if ($this->isImageColumn($column))
                            @if ($this->imageUrl($record, $column))
                                <img
                                    src="{{ $this->imageUrl($record, $column) }}"
                                    alt="{{ $this->formatCellValue($record, ['key' => $column['key']]) }}"
                                    class="size-14 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700"
                                >
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        @elseif ($this->isBooleanColumn($column))
                            <span class="{{ data_get($record, $column['key']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                                {{ data_get($record, $column['key']) ? 'Yes' : 'No' }}
                            </span>
                        @elseif ($this->isBadgeColumn($column))
                            <span class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">
                                {{ $this->formatCellValue($record, $column) }}
                            </span>
                        @elseif ($this->isCountColumn($column))
                            <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                {{ $this->formatCellValue($record, $column) }} {{ $column['suffix'] ?? 'link' }}
                            </span>
                        @else
                            {{ $this->formatCellValue($record, $column) }}
                        @endif
                    </td>
                @endforeach

                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $record->getKey() }})">
                            Edit
                        </x-dashboard.button>
                        <x-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $record->getKey() }})">
                            Delete
                        </x-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($this->resourceConfig['table_columns']) + 1 }}" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada data untuk modul ini.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            {{ $records->links() }}
        </x-slot:pagination>
    </x-dashboard.table>

    <x-dashboard.modal
        :show="$showFormModal"
        :title="$editingId ? 'Edit '.$this->resourceConfig['label'] : 'Tambah '.$this->resourceConfig['label']"
        description="Gunakan form berikut untuk menyimpan data."
        closeAction="closeFormModal"
        maxWidth="max-w-5xl"
    >
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            @foreach ($this->resourceConfig['form_fields'] as $field)
                @php
                    $fullWidth = ($field['full_width'] ?? false) === true || in_array($field['type'], ['textarea', 'json', 'image', 'image_gallery'], true);
                @endphp

                <div class="{{ $fullWidth ? 'md:col-span-2' : '' }}">
                    @if (in_array($field['type'], ['text', 'url', 'number', 'date'], true))
                        <x-dashboard.text-input
                            :label="$field['label']"
                            :name="$field['name']"
                            :type="in_array($field['type'], ['number', 'date'], true) ? $field['type'] : ($field['type'] === 'url' ? 'url' : 'text')"
                            :error="'form.'.$field['name']"
                            wire:model="form.{{ $field['name'] }}"
                        />
                    @elseif ($field['type'] === 'select')
                        <x-dashboard.select
                            :label="$field['label']"
                            :name="$field['name']"
                            :options="$this->optionsFor($field['name'])"
                            :error="'form.'.$field['name']"
                            wire:model="form.{{ $field['name'] }}"
                        />
                    @elseif (in_array($field['type'], ['textarea', 'json'], true))
                        <x-dashboard.textarea
                            :label="$field['label']"
                            :name="$field['name']"
                            :rows="$field['type'] === 'json' ? 8 : 4"
                            :error="'form.'.$field['name']"
                            wire:model="form.{{ $field['name'] }}"
                        />
                    @elseif ($field['type'] === 'image')
                        <x-dashboard.image-input
                            :label="$field['label']"
                            :name="$field['name']"
                            :current="$this->currentImageUrl($field['name'])"
                            :preview="$this->imagePreviewUrl($field['name'])"
                            :error="'imageUploads.'.$field['name']"
                            wire:model="imageUploads.{{ $field['name'] }}"
                        />
                    @elseif ($field['type'] === 'image_gallery')
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $field['label'] }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Upload banyak gambar sekaligus. Gambar pertama akan otomatis menjadi thumbnail, dan urutannya bisa diubah dengan drag and drop.</p>
                            </div>

                            @php($galleryItems = $this->imageGalleryItems($field['name']))

                            @if ($galleryItems !== [])
                                <div class="space-y-2">
                                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">Urutan Gallery</p>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                        @foreach ($galleryItems as $galleryIndex => $image)
                                            <div
                                                wire:key="{{ $field['name'] }}-gallery-{{ $image['item_key'] }}"
                                                data-gallery-item
                                                data-gallery-field="{{ $field['name'] }}"
                                                data-item-key="{{ $image['item_key'] }}"
                                                data-component-id="{{ $this->getId() }}"
                                                draggable="true"
                                                class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900/60"
                                            >
                                                <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" class="h-40 w-full object-cover">

                                                <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-3 py-3 dark:border-zinc-700">
                                                    <div class="min-w-0">
                                                        <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                                            {{ $galleryIndex === 0 ? 'Thumbnail' : 'Gallery '.($galleryIndex + 1) }}
                                                        </p>
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                            {{ $image['is_new'] ? 'Upload baru' : 'Gambar tersimpan' }}
                                                        </p>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <span class="inline-flex cursor-grab items-center rounded-xl border border-zinc-200 px-2.5 py-2 text-xs font-semibold text-zinc-500 dark:border-zinc-700 dark:text-zinc-300">
                                                            Geser
                                                        </span>
                                                        <x-dashboard.button type="button" size="sm" variant="danger" wire:click="removeImageGalleryItem('{{ $field['name'] }}', '{{ $image['item_key'] }}')">
                                                            Hapus
                                                        </x-dashboard.button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <label class="group flex cursor-pointer flex-col items-center justify-center rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-amber-400 hover:bg-amber-50 dark:border-zinc-700 dark:bg-zinc-950/60 dark:hover:border-amber-400 dark:hover:bg-amber-400/10">
                                <div class="mb-4 flex size-14 items-center justify-center rounded-full bg-white text-zinc-500 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-700">
                                    <flux:icon.photo class="size-7" />
                                </div>

                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Upload beberapa gambar</span>
                                <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG, JPEG, atau WEBP maksimal 4MB per file</span>

                                <input
                                    name="{{ $field['name'] }}"
                                    type="file"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    multiple
                                    class="sr-only"
                                    wire:model="imageUploads.{{ $field['name'] }}"
                                >
                            </label>

                            @error('imageUploads.'.$field['name'])
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror

                            @error('imageUploads.'.$field['name'].'.*')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif ($field['type'] === 'checkbox')
                        <x-dashboard.checkbox
                            :label="$field['label']"
                            :error="'form.'.$field['name']"
                            wire:model="form.{{ $field['name'] }}"
                        />
                    @elseif ($field['type'] === 'link_list')
                        <div class="space-y-3">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $field['label'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Jika hanya 1 link maka tombol akan direct. Jika lebih dari 1 link maka landing page akan menampilkan modal pilihan.</p>
                                </div>

                                <x-dashboard.button type="button" size="sm" variant="ghost" wire:click="addLinkListItem('{{ $field['name'] }}')">
                                    {{ $field['button_label'] ?? 'Tambah Item' }}
                                </x-dashboard.button>
                            </div>

                            <div class="space-y-3">
                                @foreach ($form[$field['name']] ?? [] as $index => $item)
                                    <div wire:key="{{ $field['name'] }}-{{ $index }}" class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-900/60">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                                                {{ $field['item_label'] ?? 'Item' }} {{ $index + 1 }}
                                            </p>

                                            @if (count($form[$field['name']] ?? []) > 1)
                                                <x-dashboard.button type="button" size="sm" variant="danger" wire:click="removeLinkListItem('{{ $field['name'] }}', {{ $index }})">
                                                    Hapus
                                                </x-dashboard.button>
                                            @endif
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-2">
                                            <label class="grid gap-2">
                                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Label</span>
                                                <input
                                                    type="text"
                                                    placeholder="{{ $field['label_placeholder'] ?? 'Label link' }}"
                                                    class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500"
                                                    wire:model="form.{{ $field['name'] }}.{{ $index }}.label"
                                                >
                                                @error('form.'.$field['name'].'.'.$index.'.label')
                                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                                @enderror
                                            </label>

                                            <label class="grid gap-2">
                                                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-200">URL</span>
                                                <input
                                                    type="text"
                                                    placeholder="{{ $field['url_placeholder'] ?? 'https://example.com' }}"
                                                    class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500"
                                                    wire:model="form.{{ $field['name'] }}.{{ $index }}.url"
                                                >
                                                @error('form.'.$field['name'].'.'.$index.'.url')
                                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                                @enderror
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('form.'.$field['name'])
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endforeach

            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                <x-dashboard.button variant="ghost" wire:click="closeFormModal">
                    Cancel
                </x-dashboard.button>
                <x-dashboard.button type="submit" wire:loading.attr="disabled">
                    Save Data
                </x-dashboard.button>
            </div>
        </form>
    </x-dashboard.modal>

    <x-dashboard.modal
        :show="$showDeleteModal"
        title="Hapus Data"
        description="Data yang dihapus tidak dapat dikembalikan."
        closeAction="closeDeleteModal"
        maxWidth="max-w-xl"
    >
        <div class="space-y-4">
            <p class="text-sm leading-6 text-zinc-600 dark:text-zinc-300">
                Apakah Anda yakin ingin menghapus data dari modul {{ $this->resourceConfig['label'] }}?
            </p>
        </div>

        <x-slot:footer>
            <x-dashboard.button variant="ghost" wire:click="closeDeleteModal">
                Cancel
            </x-dashboard.button>
            <x-dashboard.button variant="danger" wire:click="delete" wire:loading.attr="disabled">
                Delete
            </x-dashboard.button>
        </x-slot:footer>
    </x-dashboard.modal>
</section>

@once
    <script>
        if (! window.dashboardGallerySortInitialized) {
            window.dashboardGallerySortInitialized = true;

            let activeGalleryDrag = null;

            document.addEventListener('dragstart', (event) => {
                const item = event.target.closest('[data-gallery-item]');

                if (! item) {
                    return;
                }

                activeGalleryDrag = {
                    componentId: item.dataset.componentId,
                    fieldName: item.dataset.galleryField,
                    itemKey: item.dataset.itemKey,
                };

                item.classList.add('opacity-60');

                if (event.dataTransfer) {
                    event.dataTransfer.effectAllowed = 'move';
                    event.dataTransfer.setData('text/plain', item.dataset.itemKey);
                }
            });

            document.addEventListener('dragend', (event) => {
                const item = event.target.closest('[data-gallery-item]');
                item?.classList.remove('opacity-60');
                document.querySelectorAll('[data-gallery-item]').forEach((element) => element.classList.remove('ring-2', 'ring-amber-400'));
                activeGalleryDrag = null;
            });

            document.addEventListener('dragover', (event) => {
                if (! activeGalleryDrag) {
                    return;
                }

                const item = event.target.closest('[data-gallery-item]');

                if (! item || item.dataset.galleryField !== activeGalleryDrag.fieldName) {
                    return;
                }

                event.preventDefault();
                document.querySelectorAll('[data-gallery-item]').forEach((element) => element.classList.remove('ring-2', 'ring-amber-400'));
                item.classList.add('ring-2', 'ring-amber-400');
            });

            document.addEventListener('drop', (event) => {
                if (! activeGalleryDrag) {
                    return;
                }

                const item = event.target.closest('[data-gallery-item]');

                if (! item || item.dataset.galleryField !== activeGalleryDrag.fieldName) {
                    return;
                }

                event.preventDefault();
                item.classList.remove('ring-2', 'ring-amber-400');

                if (item.dataset.itemKey === activeGalleryDrag.itemKey) {
                    return;
                }

                window.Livewire
                    ?.find(activeGalleryDrag.componentId)
                    ?.call('moveImageGalleryItem', activeGalleryDrag.fieldName, activeGalleryDrag.itemKey, item.dataset.itemKey);
            });
        }
    </script>
@endonce
