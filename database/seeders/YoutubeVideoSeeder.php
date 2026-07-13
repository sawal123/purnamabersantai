<?php

namespace Database\Seeders;

use App\Models\LandingSetting;
use App\Models\YoutubeVideo;
use Illuminate\Database\Seeder;

class YoutubeVideoSeeder extends Seeder
{
    public function run(): void
    {
        $url = LandingSetting::query()
            ->whereNotNull('video_url')
            ->where('video_url', '!=', '')
            ->latest('id')
            ->value('video_url') ?: 'https://www.youtube.com/watch?v=yRh8YQ2m1ZU';

        YoutubeVideo::query()->updateOrCreate(
            ['title' => 'Landing YouTube Video'],
            [
                'youtube_url' => $url,
                'aria_label' => 'Purnama Bersantai YouTube video',
                'sort_order' => 0,
                'is_active' => true,
            ],
        );
    }
}
