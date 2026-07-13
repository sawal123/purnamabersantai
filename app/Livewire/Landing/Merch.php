<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\MerchandiseProduct;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Merchandise')]
class Merch extends Component
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

        $query = MerchandiseProduct::query()
            ->with([
                'images' => fn ($query) => $query->where('is_active', true),
            ])
            ->where('is_active', true)
            ->ordered();

        $totalProducts = (clone $query)->count();

        $content['merchandiseProducts'] = $query
            ->limit($this->visibleCount)
            ->get();
        $content['totalMerchandiseProducts'] = $totalProducts;
        $content['hasMoreMerchandiseProducts'] = $totalProducts > $this->visibleCount;

        return view('livewire.landing.merch', $content);
    }
}
