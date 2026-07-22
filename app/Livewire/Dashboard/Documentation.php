<?php

namespace App\Livewire\Dashboard;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts::app')]
class Documentation extends Component
{
    public function render()
    {
        return view('livewire.dashboard.documentation');
    }
}
