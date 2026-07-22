<section class="w-full space-y-6">
    <div
        class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Dashboard CRUD</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">
                    {{ $this->pageTitle }}</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    {{ $this->resourceConfig['description'] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div
                    class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div
                    class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Active</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['active'] }}</p>
                </div>
                <div
                    class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Page</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $records->currentPage() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari data..." error="search"
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

        <x-ui-dashboard.button wire:click="openPrimaryAction">
            {{ $this->actionLabel }}
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="$this->resourceConfig['table_columns']">
        @forelse ($records as $record)
            <tr wire:key="{{ $this->resource }}-{{ $record->getKey() }}" class="align-top">
                @foreach ($this->resourceConfig['table_columns'] as $column)
                    <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                        @if (($column['type'] ?? null) === 'row_number')
                            <span class="font-semibold text-slate-500 dark:text-slate-400">
                                {{ ($records->firstItem() ?? 1) + $loop->parent->index }}
                            </span>
                        @elseif ($this->isImageColumn($column))
                            @if ($this->imageUrl($record, $column))
                                <img src="{{ $this->imageUrl($record, $column) }}"
                                    alt="{{ $this->formatCellValue($record, ['key' => $column['key']]) }}"
                                    class="size-14 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
                            @else
                                <span class="text-zinc-400">-</span>
                            @endif
                        @elseif ($this->isBooleanColumn($column))
                            <span
                                class="{{ data_get($record, $column['key']) ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                                {{ data_get($record, $column['key']) ? 'Yes' : 'No' }}
                            </span>
                        @elseif ($this->isBadgeColumn($column))
                            <span
                                class="inline-flex rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-500/15 dark:text-sky-300">
                                {{ $this->formatCellValue($record, $column) }}
                            </span>
                        @elseif ($this->isCountColumn($column))
                            <span
                                class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                {{ $this->formatCellValue($record, $column) }} {{ $column['suffix'] ?? 'link' }}
                            </span>
                        @elseif ($this->isContactIconColumn($column))
                            <span
                                class="inline-flex size-10 items-center justify-center rounded-2xl bg-amber-100 text-amber-700 dark:bg-amber-500/15 dark:text-amber-300">
                                <x-contact-channel-icon :icon="data_get($record, $column['key'])" :type="data_get($record, 'type')" class="size-5" />
                            </span>
                        @elseif ($this->isReorderColumn($column))
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex min-w-10 justify-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-bold text-indigo-700 dark:bg-indigo-500/15 dark:text-indigo-300">
                                    {{ $this->formatCellValue($record, $column) }}
                                </span>

                                <div
                                    class="inline-flex overflow-hidden rounded-xl border border-slate-200 bg-white dark:border-slate-700 dark:bg-slate-900">
                                    <button type="button"
                                        class="grid size-9 place-items-center text-slate-600 transition hover:bg-slate-100 hover:text-indigo-600 disabled:cursor-not-allowed disabled:opacity-60 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
                                        title="Naikkan urutan"
                                        wire:click="moveSortOrder({{ $record->getKey() }}, 'up')"
                                        wire:loading.attr="disabled">
                                        <x-ui-dashboard.icon name="arrow-up" class="size-4" />
                                    </button>
                                    <button type="button"
                                        class="grid size-9 place-items-center border-l border-slate-200 text-slate-600 transition hover:bg-slate-100 hover:text-indigo-600 disabled:cursor-not-allowed disabled:opacity-60 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800 dark:hover:text-indigo-300"
                                        title="Turunkan urutan"
                                        wire:click="moveSortOrder({{ $record->getKey() }}, 'down')"
                                        wire:loading.attr="disabled">
                                        <x-ui-dashboard.icon name="arrow-down" class="size-4" />
                                    </button>
                                </div>
                            </div>
                        @else
                            {{ $this->formatCellValue($record, $column) }}
                        @endif
                    </td>
                @endforeach

                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $record->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger"
                            wire:click="confirmDelete({{ $record->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="{{ count($this->resourceConfig['table_columns']) + 1 }}"
                    class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada data untuk modul ini.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($records->hasPages())
                @php
                    $currentPage = $records->currentPage();
                    $lastPage = $records->lastPage();
                    $startPage = max(1, $currentPage - 2);
                    $endPage = min($lastPage, $currentPage + 2);
                @endphp

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $records->firstItem() }}-{{ $records->lastItem() }} dari {{ $records->total() }} data
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($records->onFirstPage())>
                            Prev
                        </button>

                        @if ($startPage > 1)
                            <button type="button"
                                class="grid size-10 place-items-center rounded-xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                wire:click="gotoPage(1)" wire:loading.attr="disabled">
                                1
                            </button>
                            @if ($startPage > 2)
                                <span class="px-1 text-sm font-bold text-slate-400">...</span>
                            @endif
                        @endif

                        @for ($page = $startPage; $page <= $endPage; $page++)
                            <button type="button"
                                class="{{ $page === $currentPage ? 'border-indigo-500 bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800' }} grid size-10 place-items-center rounded-xl border text-sm font-bold transition"
                                wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled">
                                {{ $page }}
                            </button>
                        @endfor

                        @if ($endPage < $lastPage)
                            @if ($endPage < $lastPage - 1)
                                <span class="px-1 text-sm font-bold text-slate-400">...</span>
                            @endif
                            <button type="button"
                                class="grid size-10 place-items-center rounded-xl border border-slate-200 text-sm font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                                wire:click="gotoPage({{ $lastPage }})" wire:loading.attr="disabled">
                                {{ $lastPage }}
                            </button>
                        @endif

                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $records->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $records->count() }} data
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit ' . $this->resourceConfig['label'] : 'Tambah ' . $this->resourceConfig['label']" description="Gunakan form berikut untuk menyimpan data."
        closeAction="closeFormModal" maxWidth="max-w-5xl"
        wire:key="dashboard-form-modal-{{ $this->resource }}">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            @if ($this->resource === 'seo-setting')
                <div class="md:col-span-2 rounded-2xl border border-amber-400/40 bg-amber-400/10 p-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white">AI SEO Generator</p>
                            <p class="mt-1 text-xs leading-5 text-zinc-600 dark:text-zinc-300">
                                Generate semua teks SEO, Open Graph, dan Twitter Card. Data teknis SEO dibuat otomatis
                                di belakang, gambar serta kode verification tetap diisi manual.
                            </p>
                        </div>

                        <x-ui-dashboard.button type="button" variant="secondary" wire:click="generateSeoTextWithAi"
                            wire:loading.attr="disabled" wire:target="generateSeoTextWithAi">
                            <span wire:loading.remove wire:target="generateSeoTextWithAi">Generate SEO dengan AI</span>
                            <span wire:loading wire:target="generateSeoTextWithAi">Generating...</span>
                        </x-ui-dashboard.button>
                    </div>
                </div>
            @endif

            @foreach ($this->resourceConfig['form_fields'] as $field)
                @if (($field['hidden_from_form'] ?? false) === true)
                    @continue
                @endif

                @php
                    $fullWidth =
                        ($field['full_width'] ?? false) === true ||
                        in_array($field['type'], ['textarea', 'json', 'rich_text', 'option_list', 'image', 'image_gallery', 'image_list'], true);
                @endphp

                <div class="{{ $fullWidth ? 'md:col-span-2' : '' }}">
                    @if (in_array($field['type'], ['text', 'url', 'number', 'date', 'datetime'], true))
                        <x-ui-dashboard.text-input :label="$field['label']" :name="$field['name']" :type="$field['type'] === 'datetime' ? 'datetime-local' : (in_array($field['type'], ['number', 'date'], true) ? $field['type'] : ($field['type'] === 'url' ? 'url' : 'text'))" :error="'form.' . $field['name']"
                            wire:model="form.{{ $field['name'] }}" />
                    @elseif ($field['type'] === 'select')
                        <label class="block">
                            <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">{{ $field['label'] }}</span>
                            <select name="{{ $field['name'] }}" wire:model="form.{{ $field['name'] }}"
                                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-500/10">
                                <option value="">Pilih {{ $field['label'] }}</option>
                                @foreach ($this->optionsFor($field['name']) as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </select>

                            @error('form.' . $field['name'])
                                <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
                            @enderror
                        </label>
                    @elseif (in_array($field['type'], ['textarea', 'json'], true))
                        <x-ui-dashboard.textarea :label="$field['label']" :name="$field['name']" :rows="$field['type'] === 'json' ? 8 : 4"
                            :error="'form.' . $field['name']" wire:model="form.{{ $field['name'] }}" />
                    @elseif ($field['type'] === 'option_list')
                        <x-ui-dashboard.textarea :label="$field['label']" :name="$field['name']" rows="4" :placeholder="$field['placeholder'] ?? 'Satu opsi per baris'"
                            :error="'form.' . $field['name']" wire:model="form.{{ $field['name'] }}" />
                        <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                            Isi satu pilihan per baris. Kosongkan jika produk tidak membutuhkan pilihan ini.
                        </p>
                    @elseif ($field['type'] === 'rich_text')
                        <x-ui-dashboard.rich-text-editor :label="$field['label']" :name="$field['name']" :error="'form.' . $field['name']"
                            wire:model="form.{{ $field['name'] }}" />
                    @elseif ($field['type'] === 'image')
                        <x-ui-dashboard.image-input :label="$field['label']" :name="$field['name']" :current="$this->currentImageUrl($field['name'])"
                            :preview="$this->imagePreviewUrl($field['name'])" :error="'imageUploads.' . $field['name']" :max-kb="$field['max_kb'] ?? 4096" :help="$field['help_text'] ?? null" wire:model="imageUploads.{{ $field['name'] }}" />
                    @elseif ($field['type'] === 'image_list')
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-100">{{ $field['label'] }}</p>
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Upload satu atau beberapa gambar. Maksimal {{ $field['max_kb'] ?? 1024 }}KB per gambar.
                                </p>
                            </div>

                            @php($imageListItems = $this->imageListItems($field['name']))
                            @php($imageListUploadPreviews = $this->imageListUploadPreviews($field['name']))

                            @if ($imageListItems !== [] || $imageListUploadPreviews !== [])
                                <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($imageListItems as $item)
                                        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-900/60" wire:key="{{ $field['name'] }}-image-list-{{ $item['index'] }}">
                                            <img src="{{ $item['url'] }}" alt="{{ $field['label'] }} {{ $item['index'] + 1 }}" class="h-40 w-full object-cover">
                                            <div class="flex items-center justify-between gap-3 border-t border-slate-200 px-3 py-3 dark:border-slate-700">
                                                <p class="min-w-0 truncate text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $item['path'] }}</p>
                                                <x-ui-dashboard.button type="button" size="sm" variant="danger" wire:click="removeImageListItem('{{ $field['name'] }}', {{ $item['index'] }})">
                                                    Hapus
                                                </x-ui-dashboard.button>
                                            </div>
                                        </div>
                                    @endforeach

                                    @foreach ($imageListUploadPreviews as $previewIndex => $preview)
                                        <div class="overflow-hidden rounded-2xl border border-indigo-200 bg-indigo-50 dark:border-indigo-500/30 dark:bg-indigo-500/10" wire:key="{{ $field['name'] }}-upload-preview-{{ $previewIndex }}">
                                            <img src="{{ $preview['url'] }}" alt="Preview {{ $preview['name'] }}" class="h-40 w-full object-cover">
                                            <div class="border-t border-indigo-200 px-3 py-3 dark:border-indigo-500/30">
                                                <p class="truncate text-sm font-semibold text-indigo-700 dark:text-indigo-200">Upload baru</p>
                                                <p class="mt-1 truncate text-xs text-indigo-500 dark:text-indigo-300">{{ $preview['name'] }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <label class="group relative flex min-h-48 cursor-pointer flex-col items-center justify-center overflow-hidden rounded-3xl border-2 border-dashed border-slate-300 bg-slate-50 p-6 text-center transition hover:border-indigo-400 hover:bg-indigo-50 dark:border-slate-700 dark:bg-slate-800/60 dark:hover:border-indigo-500 dark:hover:bg-indigo-500/5">
                                <div class="mx-auto grid h-14 w-14 place-items-center rounded-2xl bg-white text-indigo-600 shadow-lg transition group-hover:-translate-y-1 dark:bg-slate-900 dark:text-indigo-400">
                                    <x-ui-dashboard.icon name="upload" class="h-7 w-7" />
                                </div>
                                <span class="mt-4 text-sm font-extrabold text-slate-800 dark:text-slate-100">Upload gambar {{ $field['label'] }}</span>
                                <span class="mt-1 text-xs text-slate-500 dark:text-slate-400">PNG, JPG, JPEG, atau WEBP maksimal {{ $field['max_kb'] ?? 1024 }}KB per gambar</span>

                                <input
                                    name="{{ $field['name'] }}"
                                    type="file"
                                    accept="image/png,image/jpeg,image/jpg,image/webp"
                                    multiple
                                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                    wire:model="imageUploads.{{ $field['name'] }}"
                                >
                            </label>

                            @error('imageUploads.' . $field['name'])
                                <span class="text-sm font-semibold text-rose-500">{{ $message }}</span>
                            @enderror

                            @error('imageUploads.' . $field['name'] . '.*')
                                <span class="text-sm font-semibold text-rose-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif ($field['type'] === 'image_gallery')
                        <div class="space-y-4">
                            <div>
                                <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $field['label'] }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Upload banyak gambar sekaligus.
                                    Gambar pertama akan otomatis menjadi thumbnail, dan urutannya bisa diubah dengan
                                    drag and drop.</p>
                            </div>

                            @php($galleryItems = $this->imageGalleryItems($field['name']))

                            @if ($galleryItems !== [])
                                <div class="space-y-2">
                                    <p
                                        class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">
                                        Urutan Gallery</p>
                                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                        @foreach ($galleryItems as $galleryIndex => $image)
                                            <div wire:key="{{ $field['name'] }}-gallery-{{ $image['item_key'] }}"
                                                data-gallery-item data-gallery-field="{{ $field['name'] }}"
                                                data-item-key="{{ $image['item_key'] }}"
                                                data-component-id="{{ $this->getId() }}" draggable="true"
                                                class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900/60">
                                                @if (filled($image['url']))
                                                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}"
                                                        class="h-40 w-full object-cover">
                                                @else
                                                    <div
                                                        class="grid h-40 place-items-center bg-zinc-100 text-center text-xs font-semibold text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                                                        Preview sedang diproses
                                                    </div>
                                                @endif

                                                <div
                                                    class="flex items-center justify-between gap-3 border-t border-zinc-200 px-3 py-3 dark:border-zinc-700">
                                                    <div class="min-w-0">
                                                        <p
                                                            class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                                            {{ $galleryIndex === 0 ? 'Thumbnail' : 'Gallery ' . ($galleryIndex + 1) }}
                                                        </p>
                                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                            {{ $image['is_new'] ? 'Upload baru' : 'Gambar tersimpan' }}
                                                        </p>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        <span
                                                            class="inline-flex cursor-grab items-center rounded-xl border border-zinc-200 px-2.5 py-2 text-xs font-semibold text-zinc-500 dark:border-zinc-700 dark:text-zinc-300">
                                                            Geser
                                                        </span>
                                                        <x-ui-dashboard.button type="button" size="sm"
                                                            variant="danger"
                                                            wire:click="removeImageGalleryItem('{{ $field['name'] }}', '{{ $image['item_key'] }}')">
                                                            Hapus
                                                        </x-ui-dashboard.button>
                                                    </div>
                                                </div>

                                                @if (isset($field['item_title_field']))
                                                    <div
                                                        class="border-t border-zinc-200 px-3 py-3 dark:border-zinc-700">
                                                        <label class="grid gap-2">
                                                            <span
                                                                class="text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $field['item_title_label'] ?? 'Image Name' }}</span>
                                                            <input type="text"
                                                                placeholder="{{ $field['item_title_placeholder'] ?? 'Nama gambar' }}"
                                                                class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500"
                                                                value="{{ $image['title'] }}"
                                                                wire:key="{{ $field['name'] }}-title-{{ $image['item_key'] }}"
                                                                wire:input="updateImageGalleryItemTitle(@js($field['name']), @js($image['item_key']), $event.target.value)">
                                                            @error('form.' . $field['name'] . '.' . $galleryIndex .
                                                                '.title')
                                                                <span
                                                                    class="text-sm text-red-500">{{ $message }}</span>
                                                            @enderror
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <label
                                class="group relative flex cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-amber-400 hover:bg-amber-50 dark:border-zinc-700 dark:bg-zinc-950/60 dark:hover:border-amber-400 dark:hover:bg-amber-400/10">
                                <div
                                    class="mb-4 flex size-14 items-center justify-center rounded-full bg-white text-zinc-500 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-700">
                                    <x-ui-dashboard.icon name="photo" class="size-7" />
                                </div>

                                <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Upload beberapa
                                    gambar</span>
                                <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG, JPEG, atau WEBP
                                    maksimal 4MB per file</span>

                                <input name="{{ $field['name'] }}" type="file"
                                    accept="image/png,image/jpeg,image/jpg,image/webp" multiple class="absolute inset-0 h-full w-full cursor-pointer opacity-0"
                                    wire:model="imageUploads.{{ $field['name'] }}">
                            </label>

                            @error('imageUploads.' . $field['name'])
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror

                            @error('imageUploads.' . $field['name'] . '.*')
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @elseif ($field['type'] === 'checkbox')
                        <x-ui-dashboard.checkbox :label="$field['label']" :error="'form.' . $field['name']"
                            wire:model="form.{{ $field['name'] }}" />
                    @elseif ($field['type'] === 'link_list')
                        <div class="space-y-3">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                                        {{ $field['label'] }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Jika hanya 1 link maka tombol
                                        akan direct. Jika lebih dari 1 link maka landing page akan menampilkan modal
                                        pilihan.</p>
                                </div>

                                <x-ui-dashboard.button type="button" size="sm" variant="ghost"
                                    wire:click="addLinkListItem('{{ $field['name'] }}')">
                                    {{ $field['button_label'] ?? 'Tambah Item' }}
                                </x-ui-dashboard.button>
                            </div>

                            <div class="space-y-3">
                                @foreach ($form[$field['name']] ?? [] as $index => $item)
                                    <div wire:key="{{ $field['name'] }}-{{ $index }}"
                                        class="rounded-2xl border border-zinc-200 bg-zinc-50/80 p-4 dark:border-zinc-700 dark:bg-zinc-900/60">
                                        <div class="mb-3 flex items-center justify-between gap-3">
                                            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">
                                                {{ $field['item_label'] ?? 'Item' }} {{ $index + 1 }}
                                            </p>

                                            @if (count($form[$field['name']] ?? []) > 1)
                                                <x-ui-dashboard.button type="button" size="sm" variant="danger"
                                                    wire:click="removeLinkListItem('{{ $field['name'] }}', {{ $index }})">
                                                    Hapus
                                                </x-ui-dashboard.button>
                                            @endif
                                        </div>

                                        <div class="grid gap-3 md:grid-cols-2">
                                            <label class="grid gap-2">
                                                <span
                                                    class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Label</span>
                                                <input type="text"
                                                    placeholder="{{ $field['label_placeholder'] ?? 'Label link' }}"
                                                    class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500"
                                                    wire:model="form.{{ $field['name'] }}.{{ $index }}.label">
                                                @error('form.' . $field['name'] . '.' . $index . '.label')
                                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                                @enderror
                                            </label>

                                            <label class="grid gap-2">
                                                <span
                                                    class="text-sm font-medium text-zinc-700 dark:text-zinc-200">URL</span>
                                                <input type="text"
                                                    placeholder="{{ $field['url_placeholder'] ?? 'https://example.com' }}"
                                                    class="w-full rounded-2xl border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-950 outline-none transition placeholder:text-zinc-400 focus:border-amber-400 focus:ring-4 focus:ring-amber-400/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-white dark:placeholder:text-zinc-500"
                                                    wire:model="form.{{ $field['name'] }}.{{ $index }}.url">
                                                @error('form.' . $field['name'] . '.' . $index . '.url')
                                                    <span class="text-sm text-red-500">{{ $message }}</span>
                                                @enderror
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('form.' . $field['name'])
                                <span class="text-sm text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                    @endif
                </div>
            @endforeach

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
        title="Hapus data ini?"
        description="Data yang dihapus tidak dapat dikembalikan. Pastikan Anda memilih data yang benar."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>

@once
    <script>
        if (!window.dashboardGallerySortInitialized) {
            window.dashboardGallerySortInitialized = true;

            let activeGalleryDrag = null;

            document.addEventListener('dragstart', (event) => {
                const item = event.target.closest('[data-gallery-item]');

                if (!item) {
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
                document.querySelectorAll('[data-gallery-item]').forEach((element) => element.classList.remove(
                    'ring-2', 'ring-amber-400'));
                activeGalleryDrag = null;
            });

            document.addEventListener('dragover', (event) => {
                if (!activeGalleryDrag) {
                    return;
                }

                const item = event.target.closest('[data-gallery-item]');

                if (!item || item.dataset.galleryField !== activeGalleryDrag.fieldName) {
                    return;
                }

                event.preventDefault();
                document.querySelectorAll('[data-gallery-item]').forEach((element) => element.classList.remove(
                    'ring-2', 'ring-amber-400'));
                item.classList.add('ring-2', 'ring-amber-400');
            });

            document.addEventListener('drop', (event) => {
                if (!activeGalleryDrag) {
                    return;
                }

                const item = event.target.closest('[data-gallery-item]');

                if (!item || item.dataset.galleryField !== activeGalleryDrag.fieldName) {
                    return;
                }

                event.preventDefault();
                item.classList.remove('ring-2', 'ring-amber-400');

                if (item.dataset.itemKey === activeGalleryDrag.itemKey) {
                    return;
                }

                window.Livewire
                    ?.find(activeGalleryDrag.componentId)
                    ?.call('moveImageGalleryItem', activeGalleryDrag.fieldName, activeGalleryDrag.itemKey, item
                        .dataset.itemKey);
            });
        }
    </script>
@endonce
