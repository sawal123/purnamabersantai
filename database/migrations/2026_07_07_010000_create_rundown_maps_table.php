<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rundown_maps', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tahun')->index();
            $table->text('google_map')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['tahun', 'is_active']);
        });

        Schema::create('rundown_map_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rundown_map_id')->constrained('rundown_maps')->cascadeOnDelete();
            $table->string('name');
            $table->string('image_path');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rundown_map_images');
        Schema::dropIfExists('rundown_maps');
    }
};
