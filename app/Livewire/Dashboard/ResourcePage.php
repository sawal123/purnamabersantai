<?php

namespace App\Livewire\Dashboard;

use App\Support\DashboardResourceRegistry;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class ResourcePage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $resource;

    public array $resourceConfig = [];

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'qty', except: 10)]
    public int $perPage = 10;

    public array $form = [];

    public array $imageUploads = [];

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    public array $fieldOptions = [];

    protected array $perPageOptions = [10, 25, 50, 100];

    public function mount(string $resource): void
    {
        $this->resource = $resource;
        $this->resourceConfig = DashboardResourceRegistry::get($resource);
        $this->loadFieldOptions();
        $this->resetForm();
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

    public function updatedImageUploads(mixed $value, string $name): void
    {
        $fieldName = str_contains($name, '.') ? explode('.', $name)[0] : $name;
        $field = collect($this->resourceConfig['form_fields'])->firstWhere('name', $fieldName);

        if (($field['type'] ?? null) !== 'image_gallery') {
            return;
        }

        $uploads = $this->keyedImageGalleryUploads($fieldName);
        $currentItems = collect($this->form[$fieldName] ?? []);
        $currentUploadKeys = $currentItems->pluck('upload_key')->filter()->values()->all();

        foreach ($uploads as $uploadKey => $upload) {
            if (in_array($uploadKey, $currentUploadKeys, true)) {
                continue;
            }

            $currentItems->push([
                'item_key' => 'upload-'.$uploadKey,
                'id' => null,
                'path' => null,
                'title' => '',
                'alt' => '',
                'upload_key' => $uploadKey,
            ]);
        }

        $this->form[$fieldName] = $currentItems
            ->filter(function (array $item) use ($uploads) {
                $uploadKey = $item['upload_key'] ?? null;

                return $uploadKey === null || isset($uploads[$uploadKey]);
            })
            ->values()
            ->all();
    }

    public function openPrimaryAction(): void
    {
        if ($this->isSingleResource() && $this->primaryRecordId() !== null) {
            $this->edit($this->primaryRecordId());

            return;
        }

        $this->create();
    }

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->resetImageUploads();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $record = $this->baseQuery()->findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();
        $this->resetImageUploads();
        $this->fillFormFromRecord($record);
        $this->showFormModal = true;
    }

    public function closeFormModal(): void
    {
        $this->showFormModal = false;
        $this->editingId = null;
        $this->resetValidation();
        $this->resetForm();
        $this->resetImageUploads();
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
        $payload = $this->prepareForPersistence($validated['form']);
        $payload = $this->storeImageUploads($payload);

        $modelClass = $this->resourceConfig['model'];

        if ($this->editingId !== null) {
            $record = $modelClass::query()->findOrFail($this->editingId);
            $record->update($payload);
            $this->syncImageGalleryFields($record);
            $message = "{$this->resourceConfig['label']} updated successfully.";
        } else {
            $record = $modelClass::query()->create($payload);
            $this->syncImageGalleryFields($record);
            $message = "{$this->resourceConfig['label']} created successfully.";
        }

        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeFormModal();
        $this->resetPage();
        $this->loadFieldOptions();
    }

    public function delete(): void
    {
        if ($this->deletingId === null) {
            return;
        }

        $modelClass = $this->resourceConfig['model'];
        $record = $modelClass::query()->findOrFail($this->deletingId);
        $record->delete();

        $message = "{$this->resourceConfig['label']} deleted successfully.";
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
        $this->loadFieldOptions();
    }

    public function getRecordsProperty(): LengthAwarePaginator
    {
        if (! Schema::hasTable($this->tableName())) {
            return new Paginator([], 0, $this->perPage, $this->getPage());
        }

        $query = $this->baseQuery();

        if ($this->search !== '') {
            $query->where(function (Builder $builder) {
                foreach ($this->resourceConfig['searchable'] as $index => $column) {
                    if ($index === 0) {
                        $builder->where($column, 'like', '%'.$this->search.'%');
                    } else {
                        $builder->orWhere($column, 'like', '%'.$this->search.'%');
                    }
                }
            });
        }

        foreach ($this->resourceConfig['default_sort'] as $sort) {
            $query->orderBy($sort['column'], $sort['direction']);
        }

        return $query->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        $modelClass = $this->resourceConfig['model'];
        $table = $this->tableName();

        if (! Schema::hasTable($table)) {
            return ['total' => 0, 'active' => 0];
        }

        return [
            'total' => $modelClass::query()->count(),
            'active' => Schema::hasColumn($table, 'is_active')
                ? $modelClass::query()->where('is_active', true)->count()
                : 0,
        ];
    }

    public function getPageTitleProperty(): string
    {
        return $this->resourceConfig['page_title'];
    }

    public function getActionLabelProperty(): string
    {
        if ($this->isSingleResource() && $this->primaryRecordId() !== null) {
            return 'Edit Data';
        }

        return 'Tambah Data';
    }

    public function getPerPageChoicesProperty(): array
    {
        return collect($this->perPageOptions)
            ->map(fn (int $value) => ['value' => (string) $value, 'label' => "{$value} rows"])
            ->all();
    }

    public function optionsFor(string $fieldName): array
    {
        return $this->fieldOptions[$fieldName] ?? [];
    }

    public function isBooleanColumn(array $column): bool
    {
        return ($column['type'] ?? 'text') === 'boolean';
    }

    public function isBadgeColumn(array $column): bool
    {
        return ($column['type'] ?? 'text') === 'badge';
    }

    public function isCountColumn(array $column): bool
    {
        return ($column['type'] ?? 'text') === 'count';
    }

    public function isImageColumn(array $column): bool
    {
        return ($column['type'] ?? 'text') === 'image';
    }

    public function isContactIconColumn(array $column): bool
    {
        return ($column['type'] ?? 'text') === 'contact_icon';
    }

    public function imageUrl(Model $record, array $column): ?string
    {
        $value = data_get($record, $column['key']);

        if (! is_string($value) || $value === '') {
            return null;
        }

        return str_starts_with($value, 'http') || str_starts_with($value, '/')
            ? $value
            : asset($value);
    }

    public function imagePreviewUrl(string $fieldName): ?string
    {
        $upload = $this->imageUploads[$fieldName] ?? null;

        return $upload instanceof TemporaryUploadedFile ? $upload->temporaryUrl() : null;
    }

    public function imageGalleryPreviewUrls(string $fieldName): array
    {
        return collect($this->imageUploads[$fieldName] ?? [])
            ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $upload) => $upload->temporaryUrl())
            ->values()
            ->all();
    }

    public function currentImageUrl(string $fieldName): ?string
    {
        $value = $this->form[$fieldName] ?? null;

        if (! is_string($value) || $value === '') {
            return null;
        }

        return str_starts_with($value, 'http') || str_starts_with($value, '/')
            ? $value
            : asset($value);
    }

    public function currentImageGallery(string $fieldName): array
    {
        return collect($this->form[$fieldName] ?? [])
            ->map(function (array $item) {
                $path = $item['path'] ?? null;

                if (! is_string($path) || $path === '') {
                    return null;
                }

                return [
                    'id' => $item['id'] ?? null,
                    'url' => str_starts_with($path, 'http') || str_starts_with($path, '/')
                        ? $path
                        : asset($path),
                    'alt' => $item['alt'] ?? '',
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function imageGalleryItems(string $fieldName): array
    {
        $uploads = $this->keyedImageGalleryUploads($fieldName);

        return collect($this->form[$fieldName] ?? [])
            ->map(function (array $item) use ($uploads) {
                $path = $item['path'] ?? null;
                $uploadKey = $item['upload_key'] ?? null;

                if ($uploadKey && isset($uploads[$uploadKey])) {
                    return [
                        'item_key' => $item['item_key'] ?? ('upload-'.$uploadKey),
                        'url' => $uploads[$uploadKey]->temporaryUrl(),
                        'title' => $item['title'] ?? '',
                        'alt' => $item['alt'] ?? '',
                        'is_new' => true,
                    ];
                }

                if (! is_string($path) || $path === '') {
                    return null;
                }

                return [
                    'item_key' => $item['item_key'] ?? ('existing-'.($item['id'] ?? md5($path))),
                    'url' => str_starts_with($path, 'http') || str_starts_with($path, '/')
                        ? $path
                        : asset($path),
                    'title' => $item['title'] ?? '',
                    'alt' => $item['alt'] ?? '',
                    'is_new' => false,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    public function formatCellValue(Model $record, array $column): string
    {
        $value = data_get($record, $column['key']);
        $type = $column['type'] ?? 'text';

        if ($type === 'money') {
            $currency = data_get($record, $column['currency_field'] ?? 'currency', 'IDR');

            return $currency.' '.number_format((int) $value, 0, ',', '.');
        }

        if ($type === 'datetime') {
            return $value?->format('d M Y H:i') ?? '-';
        }

        if ($type === 'date') {
            return $value?->format('d M Y') ?? '-';
        }

        if ($type === 'count') {
            if (method_exists($record, 'purchaseOptions') && $column['key'] === 'purchase_links') {
                return (string) count($record->purchaseOptions());
            }

            return is_countable($value) ? (string) count($value) : '0';
        }

        $value = is_scalar($value) ? (string) $value : '-';

        if (isset($column['truncate']) && mb_strlen($value) > $column['truncate']) {
            return mb_substr($value, 0, $column['truncate']).'...';
        }

        return $value !== '' ? $value : '-';
    }

    public function render()
    {
        return view('livewire.dashboard.resource-page', [
            'records' => $this->records,
            'summary' => $this->summary,
        ]);
    }

    protected function baseQuery(): Builder
    {
        $modelClass = $this->resourceConfig['model'];
        $query = $modelClass::query();

        if (($this->resourceConfig['with'] ?? []) !== []) {
            $query->with($this->resourceConfig['with']);
        }

        return $query;
    }

    protected function loadFieldOptions(): void
    {
        $this->fieldOptions = [];

        foreach ($this->resourceConfig['form_fields'] as $field) {
            if (($field['type'] ?? 'text') !== 'select') {
                continue;
            }

            if (isset($field['options'])) {
                $this->fieldOptions[$field['name']] = $field['options'];

                continue;
            }

            if (! isset($field['options_model'])) {
                $this->fieldOptions[$field['name']] = [];

                continue;
            }

            $modelClass = $field['options_model'];
            $instance = new $modelClass;
            $table = $instance->getTable();
            $labelColumn = $field['option_label'] ?? 'name';

            if (! Schema::hasTable($table)) {
                $this->fieldOptions[$field['name']] = [];

                continue;
            }

            $this->fieldOptions[$field['name']] = $modelClass::query()
                ->orderBy($labelColumn)
                ->get(['id', $labelColumn])
                ->map(fn (Model $model) => [
                    'value' => (string) $model->getAttribute('id'),
                    'label' => (string) $model->getAttribute($labelColumn),
                ])
                ->all();
        }
    }

    protected function resetForm(): void
    {
        $form = [];

        foreach ($this->resourceConfig['form_fields'] as $field) {
            $type = $field['type'] ?? 'text';
            $default = $field['default'] ?? null;

            if ($type === 'checkbox') {
                $form[$field['name']] = (bool) ($default ?? false);

                continue;
            }

            if ($type === 'number') {
                $form[$field['name']] = $default ?? 0;

                continue;
            }

            if (in_array($type, ['date', 'datetime'], true)) {
                $form[$field['name']] = $default ?? '';

                continue;
            }

            if ($type === 'image_gallery') {
                $form[$field['name']] = [];

                continue;
            }

            if ($type === 'link_list') {
                $form[$field['name']] = $default ?? [$this->defaultLinkListItem()];

                continue;
            }

            $form[$field['name']] = $default ?? '';
        }

        $this->form = $form;
        $this->resetImageUploads();
    }

    protected function resetImageUploads(): void
    {
        $this->imageUploads = [];
    }

    protected function fillFormFromRecord(Model $record): void
    {
        foreach ($this->resourceConfig['form_fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            $value = $record->getAttribute($name);

            if ($type === 'json') {
                $this->form[$name] = $value !== null
                    ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    : '';

                continue;
            }

            if ($type === 'date') {
                $this->form[$name] = $value?->format('Y-m-d') ?? '';

                continue;
            }

            if ($type === 'datetime') {
                $this->form[$name] = $value?->format('Y-m-d\TH:i') ?? '';

                continue;
            }

            if ($type === 'image') {
                $this->form[$name] = $value ?? '';

                continue;
            }

            if ($type === 'image_gallery') {
                $relation = $field['relation'] ?? null;
                $pathField = $field['item_path_field'] ?? 'image_path';
                $altField = $field['item_alt_field'] ?? 'alt_text';
                $titleField = $field['item_title_field'] ?? null;

                $this->form[$name] = $relation && $record->relationLoaded($relation)
                    ? $record->getRelation($relation)
                        ->map(fn (Model $image) => [
                            'item_key' => 'existing-'.$image->getKey(),
                            'id' => $image->getKey(),
                            'path' => (string) $image->getAttribute($pathField),
                            'title' => $titleField ? (string) ($image->getAttribute($titleField) ?? '') : '',
                            'alt' => (string) ($image->getAttribute($altField) ?? ''),
                            'upload_key' => null,
                        ])
                        ->all()
                    : [];

                continue;
            }

            if ($type === 'checkbox') {
                $this->form[$name] = (bool) $value;

                continue;
            }

            if ($type === 'link_list') {
                $fallbackField = $field['sync_first_url_to'] ?? null;
                $fallbackUrl = $fallbackField ? $record->getAttribute($fallbackField) : null;
                $links = $this->normalizeLinkList($value, $fallbackUrl);

                $this->form[$name] = $links !== [] ? $links : [$this->defaultLinkListItem()];

                continue;
            }

            $this->form[$name] = $value ?? '';
        }
    }

    protected function prepareForPersistence(array $data): array
    {
        $payload = [];

        foreach ($this->resourceConfig['form_fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            $value = $data[$name] ?? null;

            if ($type === 'checkbox') {
                $payload[$name] = (bool) $value;

                continue;
            }

            if ($type === 'number') {
                $payload[$name] = $value === '' || $value === null ? null : (int) $value;

                continue;
            }

            if (in_array($type, ['date', 'datetime'], true)) {
                $payload[$name] = blank($value) ? null : $value;

                continue;
            }

            if ($type === 'json') {
                $payload[$name] = blank($value) ? null : json_decode($value, true, 512, JSON_THROW_ON_ERROR);

                continue;
            }

            if ($type === 'image_gallery') {
                continue;
            }

            if ($type === 'link_list') {
                $links = $this->normalizeLinkList($value);
                $payload[$name] = $links !== [] ? $links : null;

                if (isset($field['sync_first_url_to'])) {
                    $payload[$field['sync_first_url_to']] = $links[0]['url'] ?? null;
                }

                continue;
            }

            if ($type === 'image') {
                $payload[$name] = blank($value) ? null : $value;

                continue;
            }

            $payload[$name] = blank($value) ? null : $value;
        }

        return $payload;
    }

    protected function storeImageUploads(array $payload): array
    {
        foreach ($this->resourceConfig['form_fields'] as $field) {
            if (($field['type'] ?? 'text') !== 'image') {
                continue;
            }

            $name = $field['name'];
            $upload = $this->imageUploads[$name] ?? null;

            if (! $upload instanceof TemporaryUploadedFile) {
                continue;
            }

            $path = $upload->storePublicly("dashboard/uploads/{$this->resource}", 'public');
            $payload[$name] = '/storage/'.$path;
        }

        return $payload;
    }

    protected function rules(): array
    {
        $rules = [];
        $table = $this->tableName();

        foreach ($this->resourceConfig['form_fields'] as $field) {
            $name = $field['name'];
            $type = $field['type'] ?? 'text';
            $ruleSet = [];

            $ruleSet[] = ($field['required'] ?? false) && $type !== 'image' ? 'required' : 'nullable';

            if ($type === 'text' || $type === 'textarea') {
                $ruleSet[] = 'string';
                $ruleSet[] = $type === 'text' ? 'max:255' : 'max:5000';
            }

            if (in_array($type, ['date', 'datetime'], true)) {
                $ruleSet[] = 'date';
            }

            if ($type === 'url') {
                $ruleSet[] = 'url';
                $ruleSet[] = 'max:2048';
            }

            if ($type === 'number') {
                $ruleSet[] = 'integer';
                $ruleSet[] = 'min:0';
            }

            if ($type === 'checkbox') {
                $ruleSet[] = 'boolean';
            }

            if ($type === 'json') {
                $ruleSet[] = 'json';
            }

            if ($type === 'image') {
                $ruleSet[] = 'string';
                $ruleSet[] = 'max:2048';
            }

            if ($type === 'image_gallery') {
                $ruleSet[] = 'array';
            }

            if ($type === 'link_list') {
                $ruleSet[] = 'array';
            }

            if ($type === 'select') {
                if (isset($field['options_model'])) {
                    $modelClass = $field['options_model'];
                    $relatedTable = (new $modelClass)->getTable();
                    $ruleSet[] = "exists:{$relatedTable},id";
                }

                if (isset($field['options'])) {
                    $ruleSet[] = Rule::in(collect($field['options'])->pluck('value')->all());
                }
            }

            if (($field['unique'] ?? false) === true) {
                $ruleSet[] = Rule::unique($table, $name)->ignore($this->editingId);
            }

            $rules["form.{$name}"] = $ruleSet;

            if ($type === 'link_list') {
                $rules["form.{$name}.*.label"] = ['nullable', 'string', 'max:255'];
                $rules["form.{$name}.*.url"] = [
                    'nullable',
                    'string',
                    'max:2048',
                    function (string $attribute, mixed $value, \Closure $fail) use ($field) {
                        if (blank($value)) {
                            return;
                        }

                        if ($this->isValidUrlValue((string) $value, (bool) ($field['allow_relative'] ?? false))) {
                            return;
                        }

                        $fail('The '.$attribute.' field must be a valid URL.');
                    },
                ];
            }

            if ($type === 'image_gallery') {
                $rules["imageUploads.{$name}"] = ['nullable', 'array'];
                $rules["imageUploads.{$name}.*"] = [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ];

                if (isset($field['item_title_field'])) {
                    $rules["form.{$name}.*.title"] = [
                        ($field['item_title_required'] ?? false) ? 'required' : 'nullable',
                        'string',
                        'max:255',
                    ];
                }
            }

            if ($type === 'image') {
                $uploadRules = [
                    'nullable',
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:4096',
                ];

                if (($field['required'] ?? false) === true) {
                    array_unshift($uploadRules, "required_without:form.{$name}");
                }

                $rules["imageUploads.{$name}"] = $uploadRules;
            }
        }

        return $rules;
    }

    protected function validationAttributes(): array
    {
        $attributes = [];

        foreach ($this->resourceConfig['form_fields'] as $field) {
            $attributes["form.{$field['name']}"] = $field['label'];

            if (($field['type'] ?? 'text') === 'link_list') {
                $attributes["form.{$field['name']}.*.label"] = $field['label'].' label';
                $attributes["form.{$field['name']}.*.url"] = $field['label'].' URL';
            }

            if (($field['type'] ?? 'text') === 'image_gallery') {
                $attributes["imageUploads.{$field['name']}.*"] = $field['label'];

                if (isset($field['item_title_field'])) {
                    $attributes["form.{$field['name']}.*.title"] = ($field['item_title_label'] ?? 'Image Name');
                }
            }

            if (($field['type'] ?? 'text') === 'image') {
                $attributes["imageUploads.{$field['name']}"] = $field['label'];
            }
        }

        return $attributes;
    }

    public function addLinkListItem(string $fieldName): void
    {
        $this->form[$fieldName] ??= [];
        $this->form[$fieldName][] = $this->defaultLinkListItem();
    }

    public function removeLinkListItem(string $fieldName, int $index): void
    {
        $items = $this->form[$fieldName] ?? [];

        if (! array_key_exists($index, $items)) {
            return;
        }

        unset($items[$index]);
        $items = array_values($items);

        $this->form[$fieldName] = $items !== [] ? $items : [$this->defaultLinkListItem()];
    }

    public function removeImageGalleryItem(string $fieldName, string $itemKey): void
    {
        $items = collect($this->form[$fieldName] ?? []);
        $item = $items->firstWhere('item_key', $itemKey);

        if (! $item) {
            return;
        }

        $this->form[$fieldName] = $items
            ->reject(fn (array $galleryItem) => ($galleryItem['item_key'] ?? null) === $itemKey)
            ->values()
            ->all();

        $uploadKey = $item['upload_key'] ?? null;

        if ($uploadKey === null) {
            return;
        }

        $uploads = $this->keyedImageGalleryUploads($fieldName);
        unset($uploads[$uploadKey]);
        $this->imageUploads[$fieldName] = array_values($uploads);
    }

    public function moveImageGalleryItem(string $fieldName, string $draggedItemKey, string $targetItemKey): void
    {
        if ($draggedItemKey === $targetItemKey) {
            return;
        }

        $items = collect($this->form[$fieldName] ?? []);
        $dragged = $items->firstWhere('item_key', $draggedItemKey);
        $targetIndex = $items->search(fn (array $item) => ($item['item_key'] ?? null) === $targetItemKey);

        if (! is_array($dragged) || $targetIndex === false) {
            return;
        }

        $reordered = $items
            ->reject(fn (array $item) => ($item['item_key'] ?? null) === $draggedItemKey)
            ->values();

        $reordered->splice((int) $targetIndex, 0, [$dragged]);

        $this->form[$fieldName] = $reordered->values()->all();
    }

    protected function isSingleResource(): bool
    {
        return (bool) ($this->resourceConfig['single_record'] ?? false);
    }

    protected function primaryRecordId(): ?int
    {
        if (! $this->isSingleResource() || ! Schema::hasTable($this->tableName())) {
            return null;
        }

        return $this->baseQuery()->value('id');
    }

    protected function tableName(): string
    {
        $modelClass = $this->resourceConfig['model'];

        return (new $modelClass)->getTable();
    }

    protected function syncImageGalleryFields(Model $record): void
    {
        foreach ($this->resourceConfig['form_fields'] as $field) {
            if (($field['type'] ?? 'text') !== 'image_gallery') {
                continue;
            }

            $galleryItems = collect($this->form[$field['name']] ?? []);
            $relationName = $field['relation'] ?? null;

            if (! $relationName || ! method_exists($record, $relationName)) {
                continue;
            }

            $uploads = $this->keyedImageGalleryUploads($field['name']);
            $pathField = $field['item_path_field'] ?? 'image_path';
            $altField = $field['item_alt_field'] ?? 'alt_text';
            $titleField = $field['item_title_field'] ?? null;
            $classField = $field['item_class_field'] ?? 'image_class';
            $sortField = $field['item_sort_field'] ?? 'sort_order';
            $activeField = $field['item_active_field'] ?? 'is_active';
            $defaultClass = $field['default_class'] ?? null;
            $existingImages = $record->{$relationName}()->get()->keyBy(fn (Model $image) => (string) $image->getKey());
            $keptExistingIds = $galleryItems->pluck('id')->filter()->map(fn ($id) => (string) $id)->all();

            $record->{$relationName}()
                ->whereNotIn('id', $keptExistingIds === [] ? [-1] : $keptExistingIds)
                ->delete();

            $firstStoredPath = null;
            $sortOrder = 1;

            foreach ($galleryItems as $item) {
                $existingId = isset($item['id']) && $item['id'] !== null ? (string) $item['id'] : null;
                $uploadKey = $item['upload_key'] ?? null;

                if ($existingId && $existingImages->has($existingId)) {
                    $image = $existingImages->get($existingId);
                    $updates = [
                        $sortField => $sortOrder,
                        $activeField => true,
                    ];

                    if ($titleField) {
                        $updates[$titleField] = $item['title'] ?? '';
                    }

                    $image->forceFill($updates)->save();

                    $firstStoredPath ??= (string) $image->getAttribute($pathField);
                    $sortOrder++;
                    continue;
                }

                if (! $uploadKey || ! isset($uploads[$uploadKey])) {
                    continue;
                }

                $path = $uploads[$uploadKey]->storePublicly("dashboard/uploads/{$this->resource}", 'public');
                $storedPath = '/storage/'.$path;
                $firstStoredPath ??= $storedPath;

                $imagePayload = [
                    $pathField => $storedPath,
                    $altField => $item['title'] ?? ($record->getAttribute('name') ?? $record->getKey()),
                    $sortField => $sortOrder,
                    $activeField => true,
                ];

                if ($classField) {
                    $imagePayload[$classField] = $defaultClass;
                }

                if ($titleField) {
                    $imagePayload[$titleField] = $item['title'] ?? '';
                }

                $record->{$relationName}()->create($imagePayload);

                $sortOrder++;
            }

            if (($field['auto_fill_thumbnail'] ?? false) === true) {
                $record->forceFill([
                    'thumbnail_path' => $firstStoredPath,
                    'thumbnail_alt' => $record->getAttribute('thumbnail_alt') ?: ($record->getAttribute('name') ?? null),
                    'thumbnail_class' => $record->getAttribute('thumbnail_class') ?: $defaultClass,
                ])->save();
            }
        }
    }

    protected function keyedImageGalleryUploads(string $fieldName): array
    {
        return collect($this->imageUploads[$fieldName] ?? [])
            ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
            ->mapWithKeys(fn (TemporaryUploadedFile $upload) => [$upload->getFilename() => $upload])
            ->all();
    }

    protected function defaultLinkListItem(): array
    {
        return [
            'label' => '',
            'url' => '',
        ];
    }

    protected function normalizeLinkList(mixed $value, ?string $fallbackUrl = null): array
    {
        $items = collect(is_array($value) ? $value : [])
            ->map(function (mixed $item) {
                if (is_string($item)) {
                    return [
                        'label' => '',
                        'url' => trim($item),
                    ];
                }

                if (! is_array($item)) {
                    return null;
                }

                return [
                    'label' => trim((string) ($item['label'] ?? '')),
                    'url' => trim((string) ($item['url'] ?? '')),
                ];
            })
            ->filter(fn (?array $item) => $item !== null && filled($item['url']))
            ->values()
            ->all();

        if ($items !== []) {
            return $items;
        }

        if (filled($fallbackUrl)) {
            return [[
                'label' => '',
                'url' => $fallbackUrl,
            ]];
        }

        return [];
    }

    protected function isValidUrlValue(string $value, bool $allowRelative = false): bool
    {
        $value = trim($value);

        if ($value === '') {
            return true;
        }

        if ($allowRelative && str_starts_with($value, '/')) {
            return true;
        }

        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
}
