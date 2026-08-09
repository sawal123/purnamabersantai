<?php

namespace App\Livewire\Landing;

use App\Models\GalleryMoment;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

class ShareMomentForm extends Component
{
    use WithFileUploads;

    public string $shareMomentTitle = '';

    public string $shareMomentUsername = '';

    public mixed $shareMomentImage = null;

    public function submitShareMoment(): void
    {
        $this->ensureIsNotRateLimited();

        $validated = $this->validate([
            'shareMomentTitle' => ['required', 'string', 'max:255'],
            'shareMomentUsername' => ['nullable', 'string', 'max:255'],
            'shareMomentImage' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ], [], [
            'shareMomentTitle' => 'moment title',
            'shareMomentUsername' => 'username',
            'shareMomentImage' => 'image',
        ]);

        /** @var TemporaryUploadedFile $image */
        $image = $validated['shareMomentImage'];
        $path = $this->sanitizeAndStoreImage($image);

        GalleryMoment::query()->create([
            'title' => $validated['shareMomentTitle'],
            'username' => filled($validated['shareMomentUsername'])
                ? '@' . ltrim($validated['shareMomentUsername'], '@')
                : null,
            'image_path' => '/storage/' . $path,
            'alt_text' => $validated['shareMomentTitle'],
            'is_active' => false,
        ]);

        $this->reset(
            'shareMomentTitle',
            'shareMomentUsername',
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

    /**
     * Throttle share-moment submissions to 5 per IP per 10 minutes.
     */
    private function ensureIsNotRateLimited(): void
    {
        $key = 'share-moment:' . request()->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'shareMomentImage' => trans_choice(
                    'Terlalu banyak unggahan. Silakan coba lagi dalam :seconds detik.|Terlalu banyak unggahan. Silakan coba lagi dalam :seconds detik.',
                    $seconds,
                    ['seconds' => $seconds],
                ),
            ]);
        }

        RateLimiter::hit($key, 600);
    }

    /**
     * Decode, resize, strip metadata, and re-encode the uploaded image to WebP.
     * Returns the relative storage path (e.g. "landing/uploads/share-moments/abc123.webp").
     */
    private function sanitizeAndStoreImage(TemporaryUploadedFile $image): string
    {
        $maxDimension = 2048;
        $webpQuality = 80;

        // Decode from source
        $sourcePath = $image->getRealPath();
        $sourceImage = match ($image->getMimeType()) {
            'image/jpeg', 'image/jpg' => @imagecreatefromjpeg($sourcePath),
            'image/png' => @imagecreatefrompng($sourcePath),
            'image/webp' => @imagecreatefromwebp($sourcePath),
            default => null,
        };

        if ($sourceImage === false || $sourceImage === null) {
            throw ValidationException::withMessages([
                'shareMomentImage' => 'Gambar tidak valid atau rusak.',
            ]);
        }

        // Strip metadata: GD re-encoding inherently drops EXIF/comments/profiles.
        // Additionally, convert to truecolor if not already (e.g. indexed PNG).
        if (! imageistruecolor($sourceImage)) {
            $trueColor = imagecreatetruecolor(imagesx($sourceImage), imagesy($sourceImage));
            imagecopy($trueColor, $sourceImage, 0, 0, 0, 0, imagesx($sourceImage), imagesy($sourceImage));
            imagedestroy($sourceImage);
            $sourceImage = $trueColor;
        }

        // Preserve transparency for PNG/WebP sources
        imagealphablending($sourceImage, true);
        imagesavealpha($sourceImage, true);

        // Resize if needed (maintain aspect ratio)
        $width = imagesx($sourceImage);
        $height = imagesy($sourceImage);

        if ($width > $maxDimension || $height > $maxDimension) {
            $ratio = min($maxDimension / $width, $maxDimension / $height);
            $newWidth = (int) round($width * $ratio);
            $newHeight = (int) round($height * $ratio);

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($resized, true);
            imagesavealpha($resized, true);
            imagecopyresampled($resized, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
            imagedestroy($sourceImage);
            $sourceImage = $resized;
        }

        // Re-encode to WebP, store with random hash filename
        $filename = hash('sha256', uniqid('', true) . random_bytes(16)) . '.webp';
        $directory = 'landing/uploads/share-moments';
        $disk = 'public';

        $absoluteDir = storage_path("app/public/{$directory}");
        if (! is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0755, true);
        }

        $absolutePath = "{$absoluteDir}/{$filename}";
        imagewebp($sourceImage, $absolutePath, $webpQuality);
        imagedestroy($sourceImage);

        return "{$directory}/{$filename}";
    }

    public function shareMomentPreviewUrl(): ?string
    {
        if (! $this->shareMomentImage instanceof TemporaryUploadedFile) {
            return null;
        }

        try {
            return $this->shareMomentImage->temporaryUrl();
        } catch (Throwable $exception) {
            report($exception);

            return null;
        }
    }

    public function render()
    {
        return view('livewire.landing.share-moment-form');
    }
}
