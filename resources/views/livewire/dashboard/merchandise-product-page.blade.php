<section class="w-full space-y-6">
    <div
        class="overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-600 p-6 text-white shadow-xl shadow-indigo-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Dashboard CRUD</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Merchandise Products</h1>
                <p class="mt-3 text-sm leading-6 text-indigo-100 md:text-base">
                    Kelola produk merchandise utama beserta harga, thumbnail, gallery gambar, stock, dan status aktif.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Active</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['active'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-indigo-100">Page</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $records->currentPage() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari produk..." error="search"
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
            Tambah Data
        </x-ui-dashboard.button>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Image'],
        ['label' => 'Product'],
        ['label' => 'Slug'],
        ['label' => 'Price'],
        ['label' => 'Stock'],
        ['label' => 'Active'],
    ]">
        @forelse ($records as $record)
            <tr wire:key="merchandise-product-{{ $record->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    @if ($this->imageUrl($record, ['key' => 'thumbnail_path']))
                        <img src="{{ $this->imageUrl($record, ['key' => 'thumbnail_path']) }}"
                            alt="{{ $record->thumbnail_alt ?: $record->name }}"
                            class="size-14 rounded-lg object-cover ring-1 ring-zinc-200 dark:ring-zinc-700">
                    @else
                        <span class="text-zinc-400">-</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $record->name }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $record->slug }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    {{ $record->currency }} {{ number_format((int) $record->price, 0, ',', '.') }}
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">{{ $record->stock_quantity }}</td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $record->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $record->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $record->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $record->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada produk merchandise.
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
                        Menampilkan {{ $records->firstItem() }}-{{ $records->lastItem() }} dari {{ $records->total() }} produk
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
                    Menampilkan {{ $records->count() }} produk
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit Merchandise Product' : 'Tambah Merchandise Product'"
        description="Form khusus untuk produk merchandise." closeAction="closeFormModal" maxWidth="max-w-5xl"
        wire:key="merchandise-product-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Slug" name="slug" error="form.slug" wire:model="form.slug" />
            <x-ui-dashboard.text-input label="Kicker" name="kicker" error="form.kicker" wire:model="form.kicker" />
            <x-ui-dashboard.text-input label="Product Name" name="name" error="form.name" wire:model="form.name" />
            <x-ui-dashboard.text-input label="Price" name="price" type="number" error="form.price" wire:model="form.price" />

            <label class="block">
                <span class="mb-2 block text-sm font-bold text-slate-800 dark:text-slate-100">Currency</span>
                <select name="currency" wire:model="form.currency"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-950 outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100 dark:border-slate-700 dark:bg-slate-800 dark:text-white dark:focus:ring-indigo-500/10">
                    <option value="">Pilih Currency</option>
                    @foreach ($this->optionsFor('currency') as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>

                @error('form.currency')
                    <span class="mt-2 block text-sm font-semibold text-rose-500">{{ $message }}</span>
                @enderror
            </label>

            <x-ui-dashboard.text-input label="Stock Product" name="stock_quantity" type="number"
                error="form.stock_quantity" wire:model="form.stock_quantity" />

            <div class="md:col-span-2">
                <x-ui-dashboard.rich-text-editor label="Description" name="description" error="form.description"
                    wire:model="form.description" />
            </div>

            <div class="md:col-span-2">
                <x-ui-dashboard.textarea label="Size Options" name="size_options" rows="4" placeholder="S&#10;M&#10;L&#10;XL"
                    error="form.size_options" wire:model="form.size_options" />
                <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                    Isi satu pilihan per baris. Kosongkan jika produk tidak membutuhkan pilihan ini.
                </p>
            </div>

            <div class="md:col-span-2">
                <x-ui-dashboard.textarea label="Color Options" name="color_options" rows="4" placeholder="Hitam&#10;Putih&#10;Cream"
                    error="form.color_options" wire:model="form.color_options" />
                <p class="mt-2 text-xs font-medium text-slate-500 dark:text-slate-400">
                    Isi satu pilihan per baris. Kosongkan jika produk tidak membutuhkan pilihan ini.
                </p>
            </div>

            <x-ui-dashboard.text-input label="Thumbnail Alt" name="thumbnail_alt" error="form.thumbnail_alt"
                wire:model="form.thumbnail_alt" />
            <x-ui-dashboard.text-input label="Thumbnail Class" name="thumbnail_class" error="form.thumbnail_class"
                wire:model="form.thumbnail_class" />

            <div class="md:col-span-2">
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Gallery Images</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">
                            Upload banyak gambar sekaligus. Gambar pertama otomatis menjadi thumbnail, dan urutannya bisa diubah dengan drag and drop.
                        </p>
                    </div>

                    @php($galleryItems = $this->imageGalleryItems('gallery_images'))

                    @if ($galleryItems !== [])
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 dark:text-zinc-400">
                                Urutan Gallery
                            </p>
                            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($galleryItems as $galleryIndex => $image)
                                    <div wire:key="merchandise-gallery-{{ $image['item_key'] }}"
                                        data-gallery-item data-gallery-field="gallery_images"
                                        data-item-key="{{ $image['item_key'] }}"
                                        data-component-id="{{ $this->getId() }}" draggable="true"
                                        class="overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900/60">
                                        @if (filled($image['url']))
                                            <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}"
                                                class="h-40 w-full object-cover">
                                        @else
                                            <div class="grid h-40 place-items-center bg-zinc-100 text-center text-xs font-semibold text-zinc-500 dark:bg-zinc-950 dark:text-zinc-400">
                                                Preview sedang diproses
                                            </div>
                                        @endif

                                        <div class="flex items-center justify-between gap-3 border-t border-zinc-200 px-3 py-3 dark:border-zinc-700">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">
                                                    {{ $galleryIndex === 0 ? 'Thumbnail' : 'Gallery ' . ($galleryIndex + 1) }}
                                                </p>
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                                    {{ $image['is_new'] ? 'Upload baru' : 'Gambar tersimpan' }}
                                                </p>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <span class="inline-flex cursor-grab items-center rounded-xl border border-zinc-200 px-2.5 py-2 text-xs font-semibold text-zinc-500 dark:border-zinc-700 dark:text-zinc-300">
                                                    Geser
                                                </span>
                                                <x-ui-dashboard.button type="button" size="sm" variant="danger"
                                                    wire:click="removeImageGalleryItem('gallery_images', '{{ $image['item_key'] }}')">
                                                    Hapus
                                                </x-ui-dashboard.button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <label class="group relative flex cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 px-4 py-6 text-center transition hover:border-amber-400 hover:bg-amber-50 dark:border-zinc-700 dark:bg-zinc-950/60 dark:hover:border-amber-400 dark:hover:bg-amber-400/10">
                        <div class="mb-4 flex size-14 items-center justify-center rounded-full bg-white text-zinc-500 ring-1 ring-zinc-200 dark:bg-zinc-900 dark:text-zinc-400 dark:ring-zinc-700">
                            <x-ui-dashboard.icon name="photo" class="size-7" />
                        </div>

                        <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-100">Upload beberapa gambar</span>
                        <span class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">PNG, JPG, JPEG, atau WEBP maksimal 4MB per file</span>

                        <input name="gallery_images" type="file" accept="image/png,image/jpeg,image/jpg,image/webp"
                            multiple class="absolute inset-0 h-full w-full cursor-pointer opacity-0" wire:model="imageUploads.gallery_images">
                    </label>

                    @error('imageUploads.gallery_images')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror

                    @error('imageUploads.gallery_images.*')
                        <span class="text-sm text-red-500">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <x-ui-dashboard.text-input label="Sort Order" name="sort_order" type="number" error="form.sort_order"
                wire:model="form.sort_order" />

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
        title="Hapus produk ini?"
        description="Data yang dihapus tidak dapat dikembalikan. Pastikan Anda memilih produk yang benar."
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
                    ?.call('moveImageGalleryItem', activeGalleryDrag.fieldName, activeGalleryDrag.itemKey, item.dataset.itemKey);
            });
        }
    </script>
@endonce
