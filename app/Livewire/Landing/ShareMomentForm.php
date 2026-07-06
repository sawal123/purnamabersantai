<?php

namespace App\Livewire\Landing;

use App\Models\GalleryMoment;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ShareMomentForm extends Component
{
    use WithFileUploads;

    public string $shareMomentTitle = '';

    public string $shareMomentUsername = '';

    public string $shareMomentAltText = '';

    public mixed $shareMomentImage = null;

    public function submitShareMoment(): void
    {
        $validated = $this->validate([
            'shareMomentTitle' => ['required', 'string', 'max:255'],
            'shareMomentUsername' => ['nullable', 'string', 'max:255'],
            'shareMomentAltText' => ['nullable', 'string', 'max:255'],
            'shareMomentImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [], [
            'shareMomentTitle' => 'moment title',
            'shareMomentUsername' => 'username',
            'shareMomentAltText' => 'alt text',
            'shareMomentImage' => 'image',
        ]);

        /** @var TemporaryUploadedFile $image */
        $image = $validated['shareMomentImage'];
        $path = $image->storePublicly('landing/uploads/share-moments', 'public');

        GalleryMoment::query()->create([
            'title' => $validated['shareMomentTitle'],
            'username' => filled($validated['shareMomentUsername'])
                ? '@'.ltrim($validated['shareMomentUsername'], '@')
                : null,
            'image_path' => '/storage/'.$path,
            'alt_text' => $validated['shareMomentAltText'] ?: $validated['shareMomentTitle'],
            'is_active' => false,
        ]);

        $this->reset(
            'shareMomentTitle',
            'shareMomentUsername',
            'shareMomentAltText',
            'shareMomentImage',
        );
        $this->resetValidation();

        session()->flash(
            'share-moment-status',
            'Moment kamu sudah terkirim dan akan direview sebelum tampil di galeri.',
        );

        $this->dispatch('share-moment-saved');
    }

    public function removeShareMomentImage(): void
    {
        $this->reset('shareMomentImage');
        $this->resetValidation('shareMomentImage');
    }

    public function shareMomentPreviewUrl(): ?string
    {
        return $this->shareMomentImage instanceof TemporaryUploadedFile
            ? $this->shareMomentImage->temporaryUrl()
            : null;
    }

    public function render()
    {
        return view('livewire.landing.share-moment-form');
    }
}
