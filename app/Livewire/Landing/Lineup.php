<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\LineupArtist;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Lineup')]
class Lineup extends Component
{
    use LoadsLandingContent;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    public int $visibleCount = 8;

    protected int $initialVisibleCount = 8;

    public function updatedSearch(): void
    {
        $this->visibleCount = $this->initialVisibleCount;
    }

    public function loadMore(): void
    {
        $this->visibleCount = PHP_INT_MAX;
    }

    public function render()
    {
        $content = $this->landingContent();

        $query = LineupArtist::query()
            ->where('is_active', true)
            ->ordered();

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        $totalArtists = (clone $query)->count();

        $content['lineupArtists'] = $query
            ->limit($this->visibleCount)
            ->get();
        $content['totalArtists'] = $totalArtists;
        $content['hasMoreArtists'] = $totalArtists > $this->visibleCount;

        return view('livewire.landing.lineup', $content);
    }
}
