<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_us', function (Blueprint $table) {
            $table->id();
            $table->string('label')->default('About Us');
            $table->string('organization_kicker')->nullable();
            $table->string('organization_title')->nullable();
            $table->text('organization_body')->nullable();
            $table->string('history_kicker')->nullable();
            $table->string('history_title')->nullable();
            $table->text('history_body')->nullable();
            $table->string('history_cta_label')->nullable();
            $table->string('history_cta_url')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_us');
    }
};
