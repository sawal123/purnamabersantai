<?php

namespace App\Livewire\Dashboard;

use App\Models\LandingSectionHeading;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class LandingSectionHeadingPage extends Component
{
    use WithPagination;

    public string $resource = 'landing-section-heading';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'qty', except: 10)]
    public int $perPage = 10;

    public array $form = [];

    public ?int $editingId = null;

    public ?int $deletingId = null;

    public bool $showFormModal = false;

    public bool $showDeleteModal = false;

    protected array $perPageOptions = [10, 25, 50, 100];

    public function mount(): void
    {
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

    public function create(): void
    {
        $this->editingId = null;
        $this->resetForm();
        $this->resetValidation();
        $this->showFormModal = true;
    }

    public function edit(int $id): void
    {
        $heading = LandingSectionHeading::query()->findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();
        $this->form = [
            'placement' => $heading->placement,
            'label' => $heading->label,
            'kicker' => $heading->kicker ?? '',
            'title' => $heading->title,
            'highlight_text' => $heading->highlight_text ?? '',
            'after_highlight_text' => $heading->after_highlight_text ?? '',
            'subtitle' => $heading->subtitle ?? '',
            'sort_order' => $heading->sort_order,
            'is_active' => $heading->is_active,
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

        foreach (['kicker', 'highlight_text', 'after_highlight_text', 'subtitle'] as $nullableField) {
            $payload[$nullableField] = blank($payload[$nullableField] ?? null) ? null : $payload[$nullableField];
        }

        if ($this->editingId !== null) {
            LandingSectionHeading::query()->findOrFail($this->editingId)->update($payload);
            $message = 'Section heading updated successfully.';
        } else {
            LandingSectionHeading::query()->create($payload);
            $message = 'Section heading created successfully.';
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

        LandingSectionHeading::query()->findOrFail($this->deletingId)->delete();

        $message = 'Section heading deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function getHeadingsProperty(): LengthAwarePaginator
    {
        return LandingSectionHeading::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('placement', 'like', '%'.$this->search.'%')
                        ->orWhere('label', 'like', '%'.$this->search.'%')
                        ->orWhere('title', 'like', '%'.$this->search.'%')
                        ->orWhere('subtitle', 'like', '%'.$this->search.'%');
                });
            })
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => LandingSectionHeading::query()->count(),
            'active' => LandingSectionHeading::query()->where('is_active', true)->count(),
        ];
    }

    public function getPerPageChoicesProperty(): array
    {
        return collect($this->perPageOptions)
            ->map(fn (int $value) => ['value' => (string) $value, 'label' => "{$value} rows"])
            ->all();
    }

    public function render()
    {
        return view('livewire.dashboard.landing-section-heading-page', [
            'headings' => $this->headings,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'placement' => '',
            'label' => '',
            'kicker' => '',
            'title' => '',
            'highlight_text' => '',
            'after_highlight_text' => '',
            'subtitle' => '',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.placement' => [
                'required',
                'string',
                'max:100',
                Rule::unique('landing_section_headings', 'placement')->ignore($this->editingId),
            ],
            'form.label' => ['required', 'string', 'max:255'],
            'form.kicker' => ['nullable', 'string', 'max:255'],
            'form.title' => ['required', 'string', 'max:255'],
            'form.highlight_text' => ['nullable', 'string', 'max:255'],
            'form.after_highlight_text' => ['nullable', 'string', 'max:255'],
            'form.subtitle' => ['nullable', 'string', 'max:1000'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.placement' => 'placement',
            'form.label' => 'label',
            'form.kicker' => 'kicker',
            'form.title' => 'title',
            'form.highlight_text' => 'highlight text',
            'form.after_highlight_text' => 'after highlight text',
            'form.subtitle' => 'subtitle',
            'form.sort_order' => 'sort order',
            'form.is_active' => 'active',
        ];
    }
}
