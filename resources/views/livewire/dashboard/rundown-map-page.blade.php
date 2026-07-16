<section class="w-full space-y-6">
    <div class="overflow-hidden rounded-3xl bg-gradient-to-br from-orange-600 via-amber-600 to-yellow-500 p-6 text-white shadow-xl shadow-orange-500/15 sm:p-8">
        <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div class="max-w-3xl">
                <span class="inline-flex rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/20">Event Guide</span>
                <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl">Rundown & Map</h1>
                <p class="mt-3 text-sm leading-6 text-orange-50 md:text-base">
                    Kelola gambar rundown, map, dan kategori yang tampil sebagai tab di halaman landing.
                </p>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-50">Total</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['total'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-50">Active</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['active'] }}</p>
                </div>
                <div class="rounded-2xl border border-white/15 bg-white/10 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.2em] text-orange-50">Kategori</p>
                    <p class="mt-2 text-2xl font-extrabold text-white">{{ $summary['categories'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-4 xl:flex-row xl:items-end xl:justify-between">
        <div class="grid gap-4 md:grid-cols-2 xl:w-[30rem]">
            <x-ui-dashboard.text-input label="Search" name="search" placeholder="Cari rundown atau map..." error="search"
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

        <div class="flex flex-wrap gap-3">
            <x-ui-dashboard.button variant="ghost" wire:click="createCategory">
                Tambah Kategori
            </x-ui-dashboard.button>
            <x-ui-dashboard.button wire:click="create">
                Tambah Data
            </x-ui-dashboard.button>
        </div>
    </div>

    <x-ui-dashboard.table :columns="[
        ['label' => 'Data'],
        ['label' => 'Image'],
        ['label' => 'Category'],
        ['label' => 'Date'],
        ['label' => 'Active'],
    ]">
        @forelse ($items as $item)
            <tr wire:key="rundown-map-{{ $item->getKey() }}" class="align-top">
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <p class="font-bold text-slate-900 dark:text-white">{{ $item->title ?: '-' }}</p>
                    <p class="mt-1 line-clamp-2 text-xs text-slate-500 dark:text-slate-400">{{ $item->description ?: 'No description' }}</p>
                    <p class="mt-1 break-all text-xs text-slate-400">{{ $item->image_path }}</p>
                </td>
                <td class="min-w-48 px-4 py-4">
                    @if ($this->imageUrl($item->image_path))
                        <img
                            src="{{ $this->imageUrl($item->image_path) }}"
                            alt="{{ $item->title ?: 'Rundown map image' }}"
                            class="h-28 w-44 rounded-2xl border border-slate-200 object-cover dark:border-slate-700"
                            loading="lazy"
                        >
                    @else
                        <span class="text-sm text-zinc-400">No image</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-200">
                        {{ $item->category?->name ?: 'No category' }}
                    </span>
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    {{ optional($item->date)->format('d M Y') ?: '-' }}
                </td>
                <td class="px-4 py-4 text-sm text-zinc-700 dark:text-zinc-200">
                    <span
                        class="{{ $item->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                        {{ $item->is_active ? 'Yes' : 'No' }}
                    </span>
                </td>
                <td class="px-4 py-4">
                    <div class="flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="edit({{ $item->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmDelete({{ $item->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="px-4 py-12 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Belum ada data rundown/map.
                </td>
            </tr>
        @endforelse

        <x-slot:pagination>
            @if ($items->hasPages())
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                        Menampilkan {{ $items->firstItem() }}-{{ $items->lastItem() }} dari {{ $items->total() }} data
                    </p>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="previousPage" wire:loading.attr="disabled" @disabled($items->onFirstPage())>
                            Prev
                        </button>
                        <button type="button"
                            class="rounded-xl border border-slate-200 px-3 py-2 text-sm font-bold text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-45 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800"
                            wire:click="nextPage" wire:loading.attr="disabled" @disabled(! $items->hasMorePages())>
                            Next
                        </button>
                    </div>
                </div>
            @else
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">
                    Menampilkan {{ $items->count() }} data
                </p>
            @endif
        </x-slot:pagination>
    </x-ui-dashboard.table>

    <div class="rounded-3xl border border-slate-200 bg-white p-5 dark:border-slate-800 dark:bg-slate-900">
        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-lg font-extrabold text-slate-950 dark:text-white">Kategori Rundown & Map</h2>
                <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Kategori aktif akan menjadi tab di landing page.</p>
            </div>
            <x-ui-dashboard.button size="sm" wire:click="createCategory">Tambah Kategori</x-ui-dashboard.button>
        </div>

        <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($categories as $category)
                <div wire:key="rundown-map-category-{{ $category->getKey() }}" class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="font-bold text-slate-950 dark:text-white">{{ $category->name }}</p>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $category->slug }} · order {{ $category->sort_order }}</p>
                        </div>
                        <span
                            class="{{ $category->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-300' : 'bg-zinc-200 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300' }} inline-flex rounded-full px-3 py-1 text-xs font-semibold">
                            {{ $category->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <x-ui-dashboard.button size="sm" variant="ghost" wire:click="editCategory({{ $category->getKey() }})">
                            Edit
                        </x-ui-dashboard.button>
                        <x-ui-dashboard.button size="sm" variant="danger" wire:click="confirmCategoryDelete({{ $category->getKey() }})">
                            Delete
                        </x-ui-dashboard.button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <x-ui-dashboard.modal :show="$showFormModal" :title="$editingId ? 'Edit Rundown / Map' : 'Tambah Rundown / Map'"
        description="Upload gambar yang akan tampil pada tab kategori di halaman rundown map." closeAction="closeFormModal" maxWidth="max-w-5xl"
        wire:key="rundown-map-form-modal">
        <form wire:submit="save" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Title" name="title" error="form.title" wire:model="form.title" />
            <x-ui-dashboard.select label="Category" name="category_id" :options="$this->categoryChoices" error="form.category_id"
                wire:model="form.category_id" />
            <x-ui-dashboard.text-input label="Date" name="date" type="date" error="form.date" wire:model="form.date" />
            <x-ui-dashboard.checkbox label="Active" error="form.is_active" wire:model="form.is_active" />

            <div class="md:col-span-2">
                <x-ui-dashboard.textarea label="Description" name="description" rows="4" error="form.description"
                    wire:model="form.description" />
            </div>

            <div class="md:col-span-2">
                <x-ui-dashboard.image-input
                    label="Image"
                    name="imageUpload"
                    :current="$this->imageUrl($form['image_path'] ?? null)"
                    error="imageUpload"
                    wire:model="imageUpload"
                />
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

    <x-ui-dashboard.modal :show="$showCategoryModal" :title="$editingCategoryId ? 'Edit Kategori' : 'Tambah Kategori'"
        description="Kategori aktif akan tampil sebagai tab di landing rundown map." closeAction="closeCategoryModal" maxWidth="max-w-2xl"
        wire:key="rundown-map-category-modal">
        <form wire:submit="saveCategory" class="grid gap-4 md:grid-cols-2">
            <x-ui-dashboard.text-input label="Name" name="category_name" error="categoryForm.name"
                wire:model.live.debounce.150ms="categoryForm.name" />
            <x-ui-dashboard.text-input label="Slug" name="category_slug" placeholder="otomatis dari name jika kosong" error="categoryForm.slug"
                wire:model="categoryForm.slug" />
            <x-ui-dashboard.text-input label="Sort Order" name="category_sort_order" type="number" error="categoryForm.sort_order"
                wire:model="categoryForm.sort_order" />
            <x-ui-dashboard.checkbox label="Active" error="categoryForm.is_active" wire:model="categoryForm.is_active" />

            <div class="md:col-span-2 flex justify-end gap-3 pt-2">
                <x-ui-dashboard.button variant="ghost" wire:click="closeCategoryModal">
                    Cancel
                </x-ui-dashboard.button>
                <x-ui-dashboard.button type="submit" wire:loading.attr="disabled">
                    Save Category
                </x-ui-dashboard.button>
            </div>
        </form>
    </x-ui-dashboard.modal>

    <x-ui-dashboard.confirm-modal
        :show="$showDeleteModal"
        title="Hapus data ini?"
        description="Data akan masuk soft delete dan tidak tampil di landing."
        cancelAction="closeDeleteModal"
        confirmAction="delete"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />

    <x-ui-dashboard.confirm-modal
        :show="$showCategoryDeleteModal"
        title="Hapus kategori ini?"
        description="Kategori akan masuk soft delete. Data yang memakai kategori ini tidak akan tampil sebagai tab aktif jika kategorinya tidak tersedia."
        cancelAction="closeCategoryDeleteModal"
        confirmAction="deleteCategory"
        cancelLabel="Batal"
        confirmLabel="Ya, Hapus"
    />
</section>
