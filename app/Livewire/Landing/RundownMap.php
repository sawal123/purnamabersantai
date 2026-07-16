<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\RundownMapCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Rundown & Map - Purnama Bersantai')]
class RundownMap extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.rundown-map', [
            ...$this->landingContent(),
            'rundownMapCategories' => RundownMapCategory::query()
                ->with(['rundownMaps' => fn ($query) => $query
                    ->where('is_active', true)
                    ->whereNotNull('image_path')
                    ->ordered()])
                ->active()
                ->ordered()
                ->get(),
        ]);
    }
}
