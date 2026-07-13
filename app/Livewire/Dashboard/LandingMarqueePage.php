<?php

namespace App\Livewire\Dashboard;

use App\Models\LandingMarquee;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class LandingMarqueePage extends Component
{
    use WithPagination;

    public string $resource = 'landing-marquee';

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
        $marquee = LandingMarquee::query()->findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();
        $this->form = [
            'placement' => $marquee->placement,
            'label' => $marquee->label,
            'aria_label' => $marquee->aria_label ?? '',
            'primary_text' => $marquee->primary_text,
            'secondary_text' => $marquee->secondary_text ?? '',
            'repeat_count' => $marquee->repeat_count,
            'highlight_secondary' => $marquee->highlight_secondary,
            'is_active' => $marquee->is_active,
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

        if ($this->editingId !== null) {
            LandingMarquee::query()->findOrFail($this->editingId)->update($validated['form']);
            $message = 'Landing marquee updated successfully.';
        } else {
            LandingMarquee::query()->create($validated['form']);
            $message = 'Landing marquee created successfully.';
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

        LandingMarquee::query()->findOrFail($this->deletingId)->delete();

        $message = 'Landing marquee deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function getMarqueesProperty(): LengthAwarePaginator
    {
        return LandingMarquee::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('placement', 'like', '%'.$this->search.'%')
                        ->orWhere('label', 'like', '%'.$this->search.'%')
                        ->orWhere('primary_text', 'like', '%'.$this->search.'%')
                        ->orWhere('secondary_text', 'like', '%'.$this->search.'%');
                });
            })
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => LandingMarquee::query()->count(),
            'active' => LandingMarquee::query()->where('is_active', true)->count(),
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
        return view('livewire.dashboard.landing-marquee-page', [
            'marquees' => $this->marquees,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'placement' => '',
            'label' => '',
            'aria_label' => '',
            'primary_text' => '',
            'secondary_text' => '',
            'repeat_count' => 10,
            'highlight_secondary' => true,
            'is_active' => true,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.placement' => [
                'required',
                'string',
                'max:64',
                'alpha_dash',
                Rule::unique('landing_marquees', 'placement')->ignore($this->editingId),
            ],
            'form.label' => ['required', 'string', 'max:255'],
            'form.aria_label' => ['nullable', 'string', 'max:255'],
            'form.primary_text' => ['required', 'string', 'max:255'],
            'form.secondary_text' => ['nullable', 'string', 'max:255'],
            'form.repeat_count' => ['required', 'integer', 'min:1', 'max:30'],
            'form.highlight_secondary' => ['boolean'],
            'form.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.placement' => 'placement',
            'form.label' => 'label',
            'form.aria_label' => 'aria label',
            'form.primary_text' => 'primary text',
            'form.secondary_text' => 'secondary text',
            'form.repeat_count' => 'repeat count',
            'form.highlight_secondary' => 'highlight secondary',
            'form.is_active' => 'active',
        ];
    }
}
