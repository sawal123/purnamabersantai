<?php

namespace App\Livewire\Dashboard;

use App\Support\DashboardResourceRegistry;
use Flux\Flux;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Throwable;

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
        $this->prepareHiddenSeoFields();

        $validated = $this->validate($this->rules(), [], $this->validationAttributes());
        $payload = $this->prepareForPersistence($validated['form']);
        $payload = $this->storeImageUploads($payload);

        $modelClass = $this->resourceConfig['model'];
        $record = null;

        if ($this->editingId !== null) {
            $record = $modelClass::query()->findOrFail($this->editingId);

            DB::transaction(function () use ($record, &$payload) {
                $payload = $this->reserveSortOrder($payload, $record);
                $record->update($payload);
                $this->syncImageGalleryFields($record);
            });

            $message = "{$this->resourceConfig['label']} updated successfully.";
        } else {
            DB::transaction(function () use ($modelClass, &$payload, &$record) {
                $payload = $this->reserveSortOrder($payload);
                $record = $modelClass::query()->create($payload);
                $this->syncImageGalleryFields($record);
            });

            $message = "{$this->resourceConfig['label']} created successfully.";
        }

        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeFormModal();
        $this->resetPage();
        $this->loadFieldOptions();
    }

    public function generateSeoTextWithAi(): void
    {
        if ($this->resource !== 'seo-setting') {
            return;
        }

        $apiKey = config('services.openai.key');

        if (! is_string($apiKey) || trim($apiKey) === '') {
            Flux::toast(variant: 'danger', text: __('OPENAI_API_KEY belum tersedia di konfigurasi.'));

            return;
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(45)
                ->post('https://api.openai.com/v1/responses', [
                    'model' => config('services.openai.model', 'gpt-5.6-luna'),
                    'store' => false,
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => $this->seoGeneratorSystemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode($this->seoGeneratorContext(), JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        ],
                    ],
                    'text' => [
                        'format' => [
                            'type' => 'json_schema',
                            'name' => 'seo_setting_text',
                            'strict' => true,
                            'schema' => $this->seoGeneratorSchema(),
                        ],
                    ],
                ])
                ->throw()
                ->json();

            $generated = $this->decodeOpenAiJsonResponse($response);

            $this->form = array_replace($this->form, [
                'site_name' => $generated['site_name'] ?? ($this->form['site_name'] ?? 'Purnama Bersantai'),
                'meta_title' => $generated['meta_title'] ?? ($this->form['meta_title'] ?? ''),
                'meta_description' => $generated['meta_description'] ?? ($this->form['meta_description'] ?? ''),
                'meta_keywords' => $generated['meta_keywords'] ?? ($this->form['meta_keywords'] ?? ''),
                'canonical_url' => $generated['canonical_url'] ?? ($this->form['canonical_url'] ?? ''),
                'og_title' => $generated['og_title'] ?? ($this->form['og_title'] ?? ''),
                'og_description' => $generated['og_description'] ?? ($this->form['og_description'] ?? ''),
                'og_type' => $generated['og_type'] ?? ($this->form['og_type'] ?? 'website'),
                'twitter_card' => $generated['twitter_card'] ?? ($this->form['twitter_card'] ?? 'summary_large_image'),
                'twitter_title' => $generated['twitter_title'] ?? ($this->form['twitter_title'] ?? ''),
                'twitter_description' => $generated['twitter_description'] ?? ($this->form['twitter_description'] ?? ''),
                'theme_color' => $generated['theme_color'] ?? ($this->form['theme_color'] ?? '#151515'),
                'locale' => $generated['locale'] ?? ($this->form['locale'] ?? 'id_ID'),
                'schema_json' => $this->normalizeGeneratedSchemaJson($generated['schema_json'] ?? ''),
            ]);

            Flux::toast(variant: 'success', text: __('SEO text generated with AI.'));
        } catch (Throwable $exception) {
            report($exception);

            Flux::toast(variant: 'danger', text: __('Gagal generate SEO dengan AI. Cek API key, model, atau koneksi server.'));
        }
    }

    protected function prepareHiddenSeoFields(): void
    {
        if ($this->resource !== 'seo-setting') {
            return;
        }

        $this->form['schema_json'] = $this->normalizeGeneratedSchemaJson($this->form['schema_json'] ?? '')
            ?: $this->defaultSeoSchemaJson();
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

    public function moveSortOrder(int $id, string $direction): void
    {
        if (! $this->supportsSortOrderControls() || ! in_array($direction, ['up', 'down'], true)) {
            return;
        }

        $modelClass = $this->resourceConfig['model'];
        $sortField = $this->sortOrderField();

        DB::transaction(function () use ($modelClass, $sortField, $id, $direction) {
            $records = $modelClass::query()
                ->orderBy($sortField)
                ->orderBy('id')
                ->get()
                ->values();

            $currentIndex = $records->search(
                fn (Model $record) => (int) $record->getKey() === $id
            );

            if ($currentIndex === false) {
                return;
            }

            $targetIndex = $direction === 'up' ? $currentIndex - 1 : $currentIndex + 1;

            if ($targetIndex < 0 || $targetIndex >= $records->count()) {
                return;
            }

            $reordered = $records->all();
            [$reordered[$currentIndex], $reordered[$targetIndex]] = [$reordered[$targetIndex], $reordered[$currentIndex]];

            foreach ($reordered as $index => $record) {
                $nextSortOrder = $index + 1;

                if ((int) $record->getAttribute($sortField) === $nextSortOrder) {
                    continue;
                }

                $record->forceFill([$sortField => $nextSortOrder])->save();
            }
        });
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

    public function isReorderColumn(array $column): bool
    {
        return $this->supportsSortOrderControls()
            && ($column['key'] ?? null) === $this->sortOrderField();
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

        return $upload instanceof TemporaryUploadedFile ? $this->temporaryUploadUrl($upload) : null;
    }

    public function imageGalleryPreviewUrls(string $fieldName): array
    {
        return collect($this->imageUploads[$fieldName] ?? [])
            ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $upload) => $this->temporaryUploadUrl($upload))
            ->filter()
            ->values()
            ->all();
    }

    public function imageListUploadPreviews(string $fieldName): array
    {
        return collect($this->imageUploads[$fieldName] ?? [])
            ->filter(fn ($upload) => $upload instanceof TemporaryUploadedFile)
            ->map(fn (TemporaryUploadedFile $upload) => [
                'url' => $this->temporaryUploadUrl($upload),
                'name' => $upload->getClientOriginalName(),
            ])
            ->filter(fn (array $preview) => filled($preview['url']))
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

    public function imageListItems(string $fieldName): array
    {
        return collect($this->form[$fieldName] ?? [])
            ->filter(fn ($path) => is_string($path) && trim($path) !== '')
            ->map(fn (string $path, int $index) => [
                'index' => $index,
                'path' => $path,
                'url' => str_starts_with($path, 'http') || str_starts_with($path, '/')
                    ? $path
                    : asset($path),
            ])
            ->values()
            ->all();
    }

    public function currentImageGallery(string $fieldName): array
    {
        return collect($this->form[$fieldName] ?? [])
            ->filter(fn ($item) => is_array($item))
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
            ->filter(fn ($item) => is_array($item))
            ->map(function (array $item) use ($uploads) {
                $path = $item['path'] ?? null;
                $uploadKey = $item['upload_key'] ?? null;

                if ($uploadKey && isset($uploads[$uploadKey])) {
                    $temporaryUrl = $this->temporaryUploadUrl($uploads[$uploadKey]);

                    return [
                        'item_key' => $item['item_key'] ?? ('upload-'.$uploadKey),
                        'url' => $temporaryUrl,
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

    public function temporaryUploadUrl(TemporaryUploadedFile $upload): ?string
    {
        try {
            return $upload->temporaryUrl();
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
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

    protected function supportsSortOrderControls(): bool
    {
        return (bool) ($this->resourceConfig['reorderable'] ?? false)
            && is_string($this->sortOrderField())
            && $this->sortOrderField() !== '';
    }

    protected function sortOrderField(): string
    {
        return (string) ($this->resourceConfig['reorder_field'] ?? 'sort_order');
    }

    protected function reserveSortOrder(array $payload, ?Model $record = null): array
    {
        if (! $this->supportsSortOrderControls()) {
            return $payload;
        }

        $sortField = $this->sortOrderField();

        if (! array_key_exists($sortField, $payload)) {
            return $payload;
        }

        $modelClass = $this->resourceConfig['model'];
        $records = $modelClass::query()
            ->when($record !== null, fn (Builder $query) => $query->whereKeyNot($record->getKey()))
            ->orderBy($sortField)
            ->orderBy('id')
            ->get();

        $rawTarget = (int) ($payload[$sortField] ?? 0);
        $maxPosition = $records->count() + 1;
        $targetPosition = $record === null && $rawTarget <= 0
            ? $maxPosition
            : max(1, min($rawTarget, $maxPosition));

        $position = 1;

        foreach ($records as $orderedRecord) {
            if ($position === $targetPosition) {
                $position++;
            }

            if ((int) $orderedRecord->getAttribute($sortField) !== $position) {
                $orderedRecord->forceFill([$sortField => $position])->save();
            }

            $position++;
        }

        $payload[$sortField] = $targetPosition;

        return $payload;
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

            if ($type === 'image_list') {
                $form[$field['name']] = [];

                continue;
            }

            if ($type === 'option_list') {
                $form[$field['name']] = '';

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

            if ($type === 'image_list') {
                $this->form[$name] = collect(is_array($value) ? $value : [])
                    ->filter(fn ($path) => is_string($path) && trim($path) !== '')
                    ->values()
                    ->all();

                continue;
            }

            if ($type === 'checkbox') {
                $this->form[$name] = (bool) $value;

                continue;
            }

            if ($type === 'option_list') {
                $this->form[$name] = collect(is_array($value) ? $value : [])
                    ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                    ->implode("\n");

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

            if ($type === 'image_list') {
                $payload[$name] = collect(is_array($value) ? $value : [])
                    ->filter(fn ($path) => is_string($path) && trim($path) !== '')
                    ->values()
                    ->all();

                continue;
            }

            if ($type === 'option_list') {
                $payload[$name] = collect(preg_split('/\R|,/', (string) $value) ?: [])
                    ->map(fn (string $item) => trim($item))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

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
            if (! in_array(($field['type'] ?? 'text'), ['image', 'image_list'], true)) {
                continue;
            }

            $name = $field['name'];
            $upload = $this->imageUploads[$name] ?? null;

            if (($field['type'] ?? 'text') === 'image_list') {
                $uploads = collect(is_array($upload) ? $upload : [])
                    ->filter(fn ($item) => $item instanceof TemporaryUploadedFile);

                if ($uploads->isEmpty()) {
                    continue;
                }

                $storedPaths = $uploads
                    ->map(fn (TemporaryUploadedFile $item) => '/storage/'.$item->storePublicly("dashboard/uploads/{$this->resource}", 'public'))
                    ->values()
                    ->all();

                $payload[$name] = collect($payload[$name] ?? [])
                    ->merge($storedPaths)
                    ->values()
                    ->all();

                continue;
            }

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

            if (in_array($type, ['text', 'textarea', 'rich_text', 'option_list'], true)) {
                $ruleSet[] = 'string';
                $ruleSet[] = $type === 'text' ? 'max:255' : 'max:12000';
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

            if ($type === 'image_list') {
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

            if ($type === 'image_list') {
                $maxKb = (int) ($field['max_kb'] ?? 1024);

                $rules["form.{$name}.*"] = ['string', 'max:2048'];
                $rules["imageUploads.{$name}"] = ['nullable', 'array'];
                $rules["imageUploads.{$name}.*"] = [
                    'image',
                    'mimes:jpg,jpeg,png,webp',
                    'max:'.$maxKb,
                ];
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

            if (($field['type'] ?? 'text') === 'image_list') {
                $attributes["form.{$field['name']}.*"] = $field['label'];
                $attributes["imageUploads.{$field['name']}.*"] = $field['label'];
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

    public function removeImageListItem(string $fieldName, int $index): void
    {
        $items = $this->form[$fieldName] ?? [];

        if (! is_array($items) || ! array_key_exists($index, $items)) {
            return;
        }

        unset($items[$index]);
        $this->form[$fieldName] = array_values($items);
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

    public function updateImageGalleryItemTitle(string $fieldName, string $itemKey, string $title): void
    {
        $this->form[$fieldName] = collect($this->form[$fieldName] ?? [])
            ->map(function (array $item) use ($itemKey, $title) {
                if (($item['item_key'] ?? null) === $itemKey) {
                    $item['title'] = $title;
                }

                return $item;
            })
            ->values()
            ->all();
    }

    protected function isSingleResource(): bool
    {
        return (bool) ($this->resourceConfig['single_record'] ?? false);
    }

    protected function seoGeneratorSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an Indonesian SEO specialist for a music festival landing page.
Generate concise, production-ready SEO copy in Indonesian for "Purnama Bersantai".
Do not generate or modify image paths, upload references, Google verification codes, Bing verification codes, or admin-only values.
Use natural keyword coverage, avoid keyword stuffing, and keep descriptions compelling for search and social previews.
The schema_json field must be a valid JSON-LD string, not markdown and not an escaped PHP array.
Return only data that matches the requested JSON schema.
PROMPT;
    }

    protected function seoGeneratorContext(): array
    {
        return [
            'current_form' => [
                'site_name' => $this->form['site_name'] ?? null,
                'meta_title' => $this->form['meta_title'] ?? null,
                'meta_description' => $this->form['meta_description'] ?? null,
                'meta_keywords' => $this->form['meta_keywords'] ?? null,
                'canonical_url' => $this->form['canonical_url'] ?? null,
                'locale' => $this->form['locale'] ?? 'id_ID',
            ],
            'site' => [
                'app_name' => config('app.name'),
                'app_url' => config('app.url'),
                'default_site_name' => 'Purnama Bersantai',
            ],
            'content_brief' => [
                'event_type' => 'festival musik malam',
                'audience' => 'penonton musik, komunitas kreatif, pencinta event lokal, calon pembeli tiket',
                'positioning' => 'festival dengan suasana santai, komunitas, lineup pilihan, merchandise, gallery moment, ticketing resmi, sponsor partner, rundown, dan map acara',
                'language' => 'id_ID',
            ],
            'requirements' => [
                'meta_title_max_chars' => 60,
                'meta_description_min_chars' => 120,
                'meta_description_max_chars' => 160,
                'social_title_max_chars' => 70,
                'social_description_max_chars' => 200,
                'canonical_url' => $this->form['canonical_url'] ?: config('app.url'),
                'theme_color' => $this->form['theme_color'] ?: '#151515',
            ],
        ];
    }

    protected function seoGeneratorSchema(): array
    {
        return [
            'type' => 'object',
            'additionalProperties' => false,
            'required' => [
                'site_name',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'og_title',
                'og_description',
                'og_type',
                'twitter_card',
                'twitter_title',
                'twitter_description',
                'theme_color',
                'locale',
                'schema_json',
            ],
            'properties' => [
                'site_name' => ['type' => 'string'],
                'meta_title' => ['type' => 'string'],
                'meta_description' => ['type' => 'string'],
                'meta_keywords' => ['type' => 'string'],
                'canonical_url' => ['type' => 'string'],
                'og_title' => ['type' => 'string'],
                'og_description' => ['type' => 'string'],
                'og_type' => ['type' => 'string', 'enum' => ['website', 'article', 'event']],
                'twitter_card' => ['type' => 'string', 'enum' => ['summary_large_image', 'summary']],
                'twitter_title' => ['type' => 'string'],
                'twitter_description' => ['type' => 'string'],
                'theme_color' => ['type' => 'string'],
                'locale' => ['type' => 'string'],
                'schema_json' => [
                    'type' => 'string',
                    'description' => 'A valid JSON-LD string using Schema.org Event or WebSite markup.',
                ],
            ],
        ];
    }

    protected function normalizeGeneratedSchemaJson(mixed $schemaJson): string
    {
        if (is_array($schemaJson)) {
            return json_encode($schemaJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
        }

        if (! is_string($schemaJson) || trim($schemaJson) === '') {
            return '';
        }

        $decoded = json_decode($schemaJson, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $schemaJson;
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: $schemaJson;
    }

    protected function defaultSeoSchemaJson(): string
    {
        $siteName = trim((string) ($this->form['site_name'] ?? 'Purnama Bersantai')) ?: 'Purnama Bersantai';
        $metaTitle = trim((string) ($this->form['meta_title'] ?? $siteName)) ?: $siteName;
        $metaDescription = trim((string) ($this->form['meta_description'] ?? ''));
        $canonicalUrl = trim((string) ($this->form['canonical_url'] ?? '')) ?: config('app.url');
        $locale = trim((string) ($this->form['locale'] ?? 'id_ID')) ?: 'id_ID';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => $siteName,
            'headline' => $metaTitle,
            'url' => $canonicalUrl,
            'inLanguage' => str_replace('_', '-', $locale),
        ];

        if ($metaDescription !== '') {
            $schema['description'] = $metaDescription;
        }

        return json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '';
    }

    protected function decodeOpenAiJsonResponse(array $response): array
    {
        $text = $response['output_text'] ?? null;

        if (! is_string($text)) {
            $text = collect($response['output'] ?? [])
                ->flatMap(fn (array $output) => $output['content'] ?? [])
                ->pluck('text')
                ->filter(fn (mixed $value) => is_string($value) && $value !== '')
                ->first();
        }

        if (! is_string($text) || $text === '') {
            throw new \RuntimeException('OpenAI response did not include JSON text.');
        }

        $decoded = json_decode($text, true, 512, JSON_THROW_ON_ERROR);

        if (! is_array($decoded)) {
            throw new \RuntimeException('OpenAI response JSON is not an object.');
        }

        return $decoded;
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
            ->mapWithKeys(fn (TemporaryUploadedFile $upload) => [
                $this->temporaryUploadKey($upload) => $upload,
            ])
            ->all();
    }

    protected function temporaryUploadKey(TemporaryUploadedFile $upload): string
    {
        return sha1(implode('|', [
            $upload->getFilename(),
            $upload->getClientOriginalName(),
            (string) $upload->getSize(),
        ]));
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
