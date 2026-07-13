<?php

namespace App\Livewire\Dashboard;

use App\Models\YoutubeVideo;
use Flux\Flux;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts::app')]
class YoutubePage extends Component
{
    use WithPagination;

    public string $resource = 'youtube';

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
        $video = YoutubeVideo::query()->findOrFail($id);

        $this->editingId = $id;
        $this->resetValidation();
        $this->form = [
            'title' => $video->title,
            'youtube_url' => $video->youtube_url,
            'aria_label' => $video->aria_label ?? '',
            'sort_order' => $video->sort_order,
            'is_active' => $video->is_active,
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

        if (YoutubeVideo::extractYoutubeVideoId($payload['youtube_url']) === null) {
            $this->addError('form.youtube_url', 'URL YouTube tidak valid.');

            return;
        }

        if ($this->editingId !== null) {
            YoutubeVideo::query()->findOrFail($this->editingId)->update($payload);
            $message = 'YouTube video updated successfully.';
        } else {
            YoutubeVideo::query()->create($payload);
            $message = 'YouTube video created successfully.';
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

        YoutubeVideo::query()->findOrFail($this->deletingId)->delete();

        $message = 'YouTube video deleted successfully.';
        session()->flash('status', $message);
        Flux::toast(variant: 'success', text: __($message));

        $this->closeDeleteModal();
        $this->resetPage();
    }

    public function getVideosProperty(): LengthAwarePaginator
    {
        return YoutubeVideo::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($builder) {
                    $builder
                        ->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('youtube_url', 'like', '%'.$this->search.'%')
                        ->orWhere('aria_label', 'like', '%'.$this->search.'%');
                });
            })
            ->ordered()
            ->paginate($this->perPage);
    }

    public function getSummaryProperty(): array
    {
        return [
            'total' => YoutubeVideo::query()->count(),
            'active' => YoutubeVideo::query()->where('is_active', true)->count(),
        ];
    }

    public function getPerPageChoicesProperty(): array
    {
        return collect($this->perPageOptions)
            ->map(fn (int $value) => ['value' => (string) $value, 'label' => "{$value} rows"])
            ->all();
    }

    public function previewEmbedSrc(?string $url = null): ?string
    {
        $videoId = YoutubeVideo::extractYoutubeVideoId($url ?? ($this->form['youtube_url'] ?? null));

        return $videoId ? 'https://www.youtube.com/embed/'.$videoId.'?rel=0&modestbranding=1' : null;
    }

    public function render()
    {
        return view('livewire.dashboard.youtube-page', [
            'videos' => $this->videos,
            'summary' => $this->summary,
        ]);
    }

    protected function resetForm(): void
    {
        $this->form = [
            'title' => '',
            'youtube_url' => '',
            'aria_label' => '',
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    protected function rules(): array
    {
        return [
            'form.title' => ['required', 'string', 'max:255'],
            'form.youtube_url' => ['required', 'string', 'max:2048'],
            'form.aria_label' => ['nullable', 'string', 'max:255'],
            'form.sort_order' => ['nullable', 'integer', 'min:0'],
            'form.is_active' => ['boolean'],
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'form.title' => 'title',
            'form.youtube_url' => 'YouTube URL',
            'form.aria_label' => 'aria label',
            'form.sort_order' => 'sort order',
            'form.is_active' => 'active',
        ];
    }
}
