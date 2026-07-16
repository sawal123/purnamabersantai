<?php

namespace App\Livewire\Dashboard;

use App\Models\RundownMap;
use App\Models\RundownMapCategory;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class RundownMapPage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $resource = 'rundown-map';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'qty', except: 10)]
    public int $perPage = 10;

    public array $form = [];

    public array $categoryForm = [];

    public ?TemporaryUploadedFile $imageUpload = null;

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public ?int $editingCategoryId = null;

    public ?int $deletingCategoryId = null;

    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    public bool $showCategoryModal = false;

    public bool $showCategoryDeleteModal = false;

    protected array $perPageOptions = [10, 25, 50, 100];

    public function mount(): void
    {
        $this->ensureDefaultCategories();
        $this->resetForm();
        $this->resetCategoryForm();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, $this->perPageOptions, true)) {
            $this->perPage = 10;
        }

        $this->resetPage();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $item = RundownMap::query()->findOrFail($id);

        $this->editingId = $id;
        $this->imageUpload = null;
        $this->resetValidation();
        $this->form = [
            'title' => $item->title ?? '',
            'category_id' => $item->category_id ? (string) $item->category_id : '',
            'image_path' => $item->image_path ?? '',
            'date' => optional($item->date)->format('Y-m-d') ?: '',
            'description' => $item->description ?? '',
            'is_active' => (bool) $item->is_active,
        ];
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal(): void
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function save(): void
    {
        $validated = $this->validate($this->rules(), [], $this->validationAttributes());
        $payload = $validated['form'];
        $payload['category_id'] = (int) $payload['category_id'];
        $payload['tahun'] = (int) substr((string) $payload['date'], 0, 4);

        if ($this->imageUpload instanceof TemporaryUploadedFile) {
            $payload['image_path'] = $this->storeImageUpload($this->imageUpload);
        }

        if ($this->editingId !== null) {
            RundownMap::query()->findOrFail($this->editingId)->update($payload);
            $message = 'Rundown map updated successfully.';
        } else {
            RundownMap::query()->create($payload);
            $message = 'Rundown map created successfully.';
        }

        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeFormModal();
        $this->resetPage();
    }

    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        RundownMap::query()->findOrFail($this->deletingId)->delete();

        $message = 'Rundown map deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function createCategory(): void
    {
        $this->editingCategoryId = null;
        $this->resetCategoryForm();
        $this->resetValidation();
        $this->showCategoryModal = true;
    }

    public function editCategory(int $id): void
    {
        $category = RundownMapCategory::query()->findOrFail($id);

        $this->editingCategoryId = $id;
        $this->resetValidation();
        $this->categoryForm = [
            'name' => $category->name,
            'slug' => $category->slug,
            'sort_order' => $category->sort_order,
            'is_active' => (bool) $category->is_active,
        ];
        $this->showCategoryModal = true;
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->editingCategoryId = null;
        $this->resetCategoryForm();
        $this->resetValidation();
    }

    public function saveCategory(): void
    {
        $this->categoryForm['slug'] = Str::slug($this->categoryForm['slug'] ?: $this->categoryForm['name']);

        $validated = $this->validate($this->categoryRules(), [], $this->categoryValidationAttributes());
        $payload = $validated['categoryForm'];

        if ($this->editingCategoryId !== null) {
            RundownMapCategory::query()->findOrFail($this->editingCategoryId)->update($payload);
            $message = 'Category updated successfully.';
        } else {
            RundownMapCategory::query()->create($payload);
            $message = 'Category created successfully.';
        }

        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeCategoryModal();
    }

    public function confirmCategoryDelete(int $id): void
    {
        $this->deletingCategoryId = $id;
        $this->showCategoryDeleteModal = true;
    }

    public function closeCategoryDeleteModal(): void
    {
        $this->showCategoryDeleteModal = false;
        $this->deletingCategoryId = null;
    }

    public function deleteCategory(): void
    {
        if ($this->deletingCategoryId === null) {
            return;
        }

        RundownMapCategory::query()->findOrFail($this->deletingCategoryId)->delete();

        $message = 'Category deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeCategoryDeleteModal();
    }

    public function getItemsProperty(): LengthAwarePaginator
    {
        return RundownMap::query()
            ->with('category')
            ->whereNotNull('image_path')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%')
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', '%'.$this->search.'%'));
                });
            })
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getCategoriesProperty()
    {
        return RundownMapCategory::query()->ordered()->get();
    }

    public function getCategoryChoicesProperty(): array
    {
        return $this->categories
            ->where('is_active', true)
            ->map(fn (RundownMapCategory $category) => [
                'value' => (string) $category->id,
                'label' => $category->name,
            ])
            ->values()
            ->all();
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => RundownMap::query()->whereNotNull('image_path')->count(),
            'active' => RundownMap::query()->whereNotNull('image_path')->where('is_active', true)->count(),
            'categories' => RundownMapCategory::query()->count(),
        ];
    }

    public function getPerPageChoicesProperty(): array
    {
        return collect($this->perPageOptions)
            ->map(fn (int $value) => ['value' => (string) $value, 'label' => "{$value} rows"])
            ->all();
    }

    public function imageUrl(?string $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        return str_starts_with($path, 'http') || str_starts_with($path, '/')
            ? $path
            : asset($path);
    }

    public function render()
    {
        return view('livewire.dashboard.rundown-map-page', [
            'items' => $this->items,
            'categories' => $this->categories,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'title' => '',
            'category_id' => (string) optional(RundownMapCategory::query()->ordered()->first())->id,
            'image_path' => '',
            'date' => now()->format('Y-m-d'),
            'description' => '',
            'is_active' => true,
        ];
        $this->imageUpload = null;
    }

    protected function resetCategoryForm(): void
    {
        $this->categoryForm = [
            'name' => '',
            'slug' => '',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.category_id' => ['required', 'integer', 'exists:rundown_map_categories,id'],
            'form.image_path' => ['nullable', 'string', 'max:2048'],
            'form.date' => ['required', 'date'],
            'form.description' => ['nullable', 'string'],
            'form.is_active' => ['boolean'],
            'imageUpload' => [
                $this->editingId === null && blank($this->form['image_path'] ?? null) ? 'required' : 'nullable',
                'image',
                'mimes:png,jpg,jpeg,webp',
                'max:4096',
            ],
        ];
    }

    protected function categoryRules(): array
    {
        $id = $this->editingCategoryId;

        return [
            'categoryForm.name' => ['required', 'string', 'max:120'],
            'categoryForm.slug' => ['nullable', 'string', 'max:140', 'unique:rundown_map_categories,slug,'.($id ?: 'NULL').',id'],
            'categoryForm.sort_order' => ['nullable', 'integer', 'min:0'],
            'categoryForm.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.title' => 'title',
            'form.category_id' => 'category',
            'form.image_path' => 'image',
            'form.date' => 'date',
            'form.description' => 'description',
            'form.is_active' => 'active',
            'imageUpload' => 'image',
        ];
    }

    protected function categoryValidationAttributes(): array
    {
        return [
            'categoryForm.name' => 'category name',
            'categoryForm.slug' => 'category slug',
            'categoryForm.sort_order' => 'sort order',
            'categoryForm.is_active' => 'active',
        ];
    }

    protected function storeImageUpload(TemporaryUploadedFile $upload): string
    {
        $directory = public_path('storage/dashboard/rundown-map');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($upload->getClientOriginalExtension() ?: $upload->extension() ?: 'jpg');
        $baseName = Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'rundown-map';
        $filename = $baseName.'-'.now()->format('YmdHis').'.'.$extension;

        File::copy($upload->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);

        return '/storage/dashboard/rundown-map/'.$filename;
    }

    protected function ensureDefaultCategories(): void
    {
        foreach ([
            ['name' => 'Rundown', 'slug' => 'rundown', 'sort_order' => 10],
            ['name' => 'Map', 'slug' => 'map', 'sort_order' => 20],
        ] as $category) {
            RundownMapCategory::query()->firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name'], 'sort_order' => $category['sort_order'], 'is_active' => true],
            );
        }
    }
}
