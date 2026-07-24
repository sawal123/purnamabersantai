<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(DashboardDummySeeder::class);
        $this->call(SongSeeder::class);
        $this->call(LandingMarqueeSeeder::class);
        $this->call(YoutubeVideoSeeder::class);
        $this->call(LandingSectionHeadingSeeder::class);
        $this->call(AboutUsSeeder::class);
    }
}
