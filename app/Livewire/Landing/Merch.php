<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Merchandise')]
class Merch extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.merch', $this->landingContent());
    }
}
