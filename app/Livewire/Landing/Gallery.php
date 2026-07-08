<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\GalleryMoment;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Gallery')]
class Gallery extends Component
{
    use LoadsLandingContent;

    public int $visibleCount = 8;

    public function loadMore(): void
    {
        $this->visibleCount = PHP_INT_MAX;
    }

    public function render()
    {
        $content = $this->landingContent();

        $query = GalleryMoment::query()
            ->where('is_active', true)
            ->ordered();

        $totalMoments = (clone $query)->count();

        $content['galleryMoments'] = $query
            ->limit($this->visibleCount)
            ->get();
        $content['totalGalleryMoments'] = $totalMoments;
        $content['hasMoreGalleryMoments'] = $totalMoments > $this->visibleCount;

        return view('livewire.landing.gallery', $content);
    }
}
