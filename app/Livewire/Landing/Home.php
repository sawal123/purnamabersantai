<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Purnama Bersantai')]
class Home extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.home', $this->landingContent());
    }
}
