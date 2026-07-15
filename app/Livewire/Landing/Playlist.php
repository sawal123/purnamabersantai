<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\SpotifyPlaylist;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Playlist')]
class Playlist extends Component
{
    use LoadsLandingContent;

    public function render()
    {
        return view('livewire.landing.playlist', [
            ...$this->landingContent(),
            'spotifyPlaylist' => SpotifyPlaylist::query()
                ->where('is_active', true)
                ->ordered()
                ->first(),
        ]);
    }
}
