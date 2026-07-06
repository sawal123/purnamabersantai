<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('order_by')->default(0)->index();
            $table->string('title');
            $table->unsignedSmallInteger('tahun')->index();
            $table->string('lokasi');
            $table->unsignedInteger('capacity')->default(0);
            $table->date('tanggal_acara')->nullable();
            $table->text('content')->nullable();
            $table->string('thumbnail')->nullable();
            $table->json('media')->nullable();
            $table->json('festival_galery')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histories');
    }
};
