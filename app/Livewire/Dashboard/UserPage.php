<?php

namespace App\Livewire\Dashboard;

use App\Models\User;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class UserPage extends Component
{
    use WithPagination;

    public string $resource = 'user';

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
        $user = User::query()->findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();
        $this->form = [
            'name' => $user->name,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
            'email_verified' => $user->email_verified_at !== null,
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
        $payload = [
            'name' => $validated['form']['name'],
            'email' => $validated['form']['email'],
            'email_verified_at' => (bool) ($validated['form']['email_verified'] ?? false) ? Carbon::now() : null,
        ];

        if (filled($validated['form']['password'] ?? null)) {
            $payload['password'] = $validated['form']['password'];
        }

        if ($this->editingId !== null) {
            User::query()->findOrFail($this->editingId)->forceFill($payload)->save();
            $message = 'User updated successfully.';
        } else {
            $user = new User();
            $user->forceFill($payload)->save();
            $message = 'User created successfully.';
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

        if ($this->deletingId === auth()->id()) {
            Flux::toast(variant: 'danger', text: __('Akun yang sedang login tidak bisa dihapus.'));
            $this->closeDeleteModal();

            return;
        }

        if (User::query()->count() <= 1) {
            Flux::toast(variant: 'danger', text: __('Minimal harus ada satu user dashboard.'));
            $this->closeDeleteModal();

            return;
        }

        User::query()->findOrFail($this->deletingId)->delete();

        $message = 'User deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function getUsersProperty(): LengthAwarePaginator
    {
        return User::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->orderBy('id')
            ->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => User::query()->count(),
            'verified' => User::query()->whereNotNull('email_verified_at')->count(),
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
        return view('livewire.dashboard.user-page', [
            'users' => $this->users,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'name' => '',
            'email' => '',
            'password' => '',
            'password_confirmation' => '',
            'email_verified' => true,
        ];
    }

    protected function rules(): array
    {
        $passwordRules = $this->editingId === null
            ? ['required', 'string', 'min:8', 'confirmed']
            : ['nullable', 'string', 'min:8', 'confirmed'];

        return [
            'form.name' => ['required', 'string', 'max:255'],
            'form.email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->editingId),
            ],
            'form.password' => $passwordRules,
            'form.password_confirmation' => [$this->editingId === null ? 'required' : 'nullable', 'string'],
            'form.email_verified' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.name' => 'name',
            'form.email' => 'email',
            'form.password' => 'password',
            'form.password_confirmation' => 'password confirmation',
            'form.email_verified' => 'email verified',
        ];
    }
}
