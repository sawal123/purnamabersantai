<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Support\FestivalHistory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('History')]
class History extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.history', [
            ...$this->landingContent(),
            'festivalHistory' => FestivalHistory::all(),
        ]);
    }
}
