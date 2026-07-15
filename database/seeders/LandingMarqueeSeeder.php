<?php

namespace Database\Seeders;

use App\Models\LandingMarquee;
use Illuminate\Database\Seeder;

class LandingMarqueeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->defaults() as $marquee) {
            LandingMarquee::query()->updateOrCreate(
                ['placement' => $marquee['placement']],
                $marquee,
            );
        }
    }

    protected function defaults(): array
    {
        return [
            [
                'placement' => 'lineup',
                'label' => 'Lineup Marquee',
                'aria_label' => 'Purnama Bersantai 2026',
                'primary_text' => 'PURNAMA BERSANTAI',
                'secondary_text' => '2026',
                'repeat_count' => 8,
                'highlight_secondary' => true,
                'is_active' => true,
            ],
            [
                'placement' => 'lineup_ticket',
                'label' => 'Lineup to Ticket Marquee',
                'aria_label' => 'Official event ticket marquee',
                'primary_text' => 'Get Your Ticket',
                'secondary_text' => 'Official Event Pass',
                'repeat_count' => 10,
                'highlight_secondary' => true,
                'is_active' => true,
            ],
            [
                'placement' => 'tickets_merch',
                'label' => 'Tickets & Merchandise Marquee',
                'aria_label' => 'Official merchandise and tickets',
                'primary_text' => 'Official Tickets',
                'secondary_text' => 'Merchandise Drop',
                'repeat_count' => 10,
                'highlight_secondary' => true,
                'is_active' => true,
            ],
            [
                'placement' => 'gallery',
                'label' => 'Gallery Marquee',
                'aria_label' => 'Beautiful festival moments',
                'primary_text' => 'Beautiful Moments',
                'secondary_text' => 'Share Your Story',
                'repeat_count' => 10,
                'highlight_secondary' => true,
                'is_active' => true,
            ],
        ];
    }
}
