<?php

namespace App\Livewire\Landing;

use App\Livewire\Landing\Concerns\LoadsLandingContent;
use App\Models\FrequentlyAskedQuestion;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts::landing')]
#[Title('FAQ')]
class Faq extends Component
{
    use LoadsLandingContent;

    public array $openFaqIds = [];

    public function toggleFaq(int $id): void
    {
        if ($this->isFaqOpen($id)) {
            $this->openFaqIds = array_values(array_filter(
                $this->openFaqIds,
                fn (int $openId) => $openId !== $id,
            ));

            return;
        }

        $this->openFaqIds[] = $id;
    }

    public function isFaqOpen(int $id): bool
    {
        return in_array($id, $this->openFaqIds, true);
    }

    public function render()
    {
        return view('livewire.landing.faq', [
            ...$this->landingContent(),
            'faqs' => FrequentlyAskedQuestion::query()
                ->where('is_active', true)
                ->ordered()
                ->get(),
        ]);
    }
}
