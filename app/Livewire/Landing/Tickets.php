<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\Ticket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('Tickets')]
class Tickets extends Component
{
    use LoadsLandingContent;

    public int $visibleCount = 8;

    public function loadMore(): void
    {
        $this->visibleCount = PHP_INT_MAX;
    }

    public function render()
    {
        $content = $this->landingContent();

        $query = Ticket::query()
            ->where('is_active', true)
            ->ordered();

        $totalTickets = (clone $query)->count();

        $content['tickets'] = $query
            ->limit($this->visibleCount)
            ->get();
        $content['totalTickets'] = $totalTickets;
        $content['hasMoreTickets'] = $totalTickets > $this->visibleCount;

        return view('livewire.landing.tickets', $content);
    }
}
