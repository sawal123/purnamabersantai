<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Support\FestivalHistory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('About')]
class About extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.about', [
            ...$this->landingContent(),
            'festivalHistory' => FestivalHistory::latest(),
        ]);
    }
}
