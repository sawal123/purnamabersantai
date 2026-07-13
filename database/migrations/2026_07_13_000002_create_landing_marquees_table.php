<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('landing_marquees', function (Blueprint $table) {
            $table->id();
            $table->string('placement')->unique();
            $table->string('label');
            $table->string('aria_label')->nullable();
            $table->string('primary_text');
            $table->string('secondary_text')->nullable();
            $table->unsignedTinyInteger('repeat_count')->default(10);
            $table->boolean('highlight_secondary')->default(true);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('landing_marquees');
    }
};
