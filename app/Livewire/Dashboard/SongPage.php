<?php

namespace App\Livewire\Dashboard;

use App\Models\Song;
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
class SongPage extends Component
{
    use WithFileUploads;
    use WithPagination;

    public string $resource = 'song';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'qty', except: 10)]
    public int $perPage = 10;

    public array $form = [];

    public ?TemporaryUploadedFile $audioUpload = null;

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
        $song = Song::query()->findOrFail($id);

        $this->editingId = $id;
        $this->audioUpload = null;
        $this->resetValidation();
        $this->form = [
            'title' => $song->title,
            'artist' => $song->artist ?? '',
            'audio_path' => $song->audio_path,
            'duration_label' => $song->duration_label ?? '',
            'sort_order' => $song->sort_order,
            'is_active' => $song->is_active,
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

        if ($this->audioUpload instanceof TemporaryUploadedFile) {
            $payload['audio_path'] = $this->storeAudioUpload($this->audioUpload);
        }

        if (blank($payload['audio_path'] ?? null)) {
            $this->addError('audioUpload', 'File lagu wajib diupload.');

            return;
        }

        if ($this->editingId !== null) {
            Song::query()->findOrFail($this->editingId)->update($payload);
            $message = 'Song updated successfully.';
        } else {
            Song::query()->create($payload);
            $message = 'Song created successfully.';
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

        Song::query()->findOrFail($this->deletingId)->delete();

        $message = 'Song deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function getSongsProperty(): LengthAwarePaginator
    {
        return Song::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('artist', 'like', '%'.$this->search.'%')
                        ->orWhere('audio_path', 'like', '%'.$this->search.'%');
                });
            })
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => Song::query()->count(),
            'active' => Song::query()->where('is_active', true)->count(),
        ];
    }

    public function getPerPageChoicesProperty(): array
    {
        return collect($this->perPageOptions)
            ->map(fn (int $value) => ['value' => (string) $value, 'label' => "{$value} rows"])
            ->all();
    }

    public function audioUrl(?string $path): ?string
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
        return view('livewire.dashboard.song-page', [
            'songs' => $this->songs,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'title' => '',
            'artist' => '',
            'audio_path' => '',
            'duration_label' => '',
            'sort_order' => 0,
            'is_active' => true,
        ];
        $this->audioUpload = null;
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.artist' => ['nullable', 'string', 'max:255'],
            'form.audio_path' => ['nullable', 'string', 'max:2048'],
            'form.duration_label' => ['nullable', 'string', 'max:32'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.is_active' => ['boolean'],
            'audioUpload' => [
                $this->editingId === null && blank($this->form['audio_path'] ?? null) ? 'required' : 'nullable',
                'file',
                'mimes:mp3,wav,ogg,m4a',
                'max:20480',
            ],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.title' => 'title',
            'form.artist' => 'artist',
            'form.audio_path' => 'audio path',
            'form.duration_label' => 'duration',
            'form.sort_order' => 'sort order',
            'form.is_active' => 'active',
            'audioUpload' => 'audio file',
        ];
    }

    protected function storeAudioUpload(TemporaryUploadedFile $upload): string
    {
        $directory = public_path('song');
        File::ensureDirectoryExists($directory);

        $extension = strtolower($upload->getClientOriginalExtension() ?: $upload->extension() ?: 'mp3');
        $baseName = Str::slug(pathinfo($upload->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'song';
        $filename = $baseName.'-'.now()->format('YmdHis').'.'.$extension;

        File::copy($upload->getRealPath(), $directory.DIRECTORY_SEPARATOR.$filename);

        return 'song/'.$filename;
    }
}
