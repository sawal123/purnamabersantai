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
        $content = $this->landingContent();

        foreach (['lineupArtists', 'tickets', 'merchandiseProducts', 'galleryMoments'] as $key) {
            if (isset($content[$key]) && method_exists($content[$key], 'take')) {
                $content[$key] = $content[$key]->take(10)->values();
            }
        }

        return view('livewire.landing.home', $content);
    }
}
