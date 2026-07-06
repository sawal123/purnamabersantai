<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Sponsor & Partner')]
class SponsorPartners extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.sponsor-partners', $this->landingContent());
    }
}
