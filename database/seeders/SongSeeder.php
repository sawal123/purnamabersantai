<?php

namespace Database\Seeders;

use App\Models\Song;
use Illuminate\Database\Seeder;

class SongSeeder extends Seeder
{
    public function run(): void
    {
        $songFiles = collect(['mp3', 'wav', 'ogg', 'm4a'])
            ->flatMap(fn (string $extension) => glob(public_path("song/*.{$extension}")) ?: [])
            ->values();

        foreach ($songFiles as $index => $songFile) {
            $filename = basename($songFile);
            $title = pathinfo($filename, PATHINFO_FILENAME);

            Song::query()->updateOrCreate(
                ['audio_path' => 'song/'.$filename],
                [
                    'title' => $title,
                    'artist' => 'Purnama Bersantai',
                    'sort_order' => $index + 1,
                    'is_active' => $index === 0,
                ],
            );
        }
    }
}
