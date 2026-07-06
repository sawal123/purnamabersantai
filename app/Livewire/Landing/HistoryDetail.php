<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Support\FestivalHistory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('History Detail')]
class HistoryDetail extends Component
{
    use LoadsLandingContent;

    public array $history = [];

    public function mount(string $title): void
    {
        $history = FestivalHistory::findByTitle($title);

        abort_unless($history, 404);

        $this->history = $history;
    }

    public function render()
    {
        $relatedHistory = collect(FestivalHistory::all())
            ->reject(fn (array $history) => ($history['slug'] ?? null) === ($this->history['slug'] ?? null))
            ->take(4)
            ->values()
            ->all();

        return view('livewire.landing.history-detail', [
            ...$this->landingContent(),
            'history' => $this->history,
            'relatedHistory' => $relatedHistory,
        ]);
    }
}
