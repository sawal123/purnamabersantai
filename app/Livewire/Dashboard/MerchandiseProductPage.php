<?php

namespace App\Livewire\Dashboard;

use App\Support\DashboardResourceRegistry;
use Livewire\Attributes\Layout;

#[Layout('layouts::app')]
class MerchandiseProductPage extends ResourcePage
{
    public function mount(string $resource = 'merchandise-product'): void
    {
        $this->resource = 'merchandise-product';
        $this->resourceConfig = DashboardResourceRegistry::get($this->resource);
        $this->loadFieldOptions();
        $this->resetForm();
    }

    public function render()
    {
        return view('livewire.dashboard.merchandise-product-page', [
            'records' => $this->records,
            'summary' => $this->summary,
        ]);
    }
}
