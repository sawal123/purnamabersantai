<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spotify_playlists', function (Blueprint $table) {
            $table->id();
            $table->string('label')->default('Spotify');
            $table->string('title');
            $table->text('embed_url');
            $table->text('open_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('spotify_playlists')->insert([
            'label' => 'Spotify',
            'title' => 'Festival Warm Up',
            'embed_url' => 'https://open.spotify.com/embed/playlist/3ijdciOT0zodS2QVoUS0n3?utm_source=generator&theme=0&si=51050317e5c34125',
            'open_url' => 'https://open.spotify.com/playlist/3ijdciOT0zodS2QVoUS0n3?si=26a07152a7914027',
            'sort_order' => 1,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('spotify_playlists');
    }
};
