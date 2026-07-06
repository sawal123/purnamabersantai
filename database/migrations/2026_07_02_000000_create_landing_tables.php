<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_settings', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->default('Purnama Bersantai');
            $table->string('page_title')->default('Purnama Bersantai');
            $table->string('hero_tagline')->nullable();
            $table->text('hero_description')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('hero_brand_path')->nullable();
            $table->string('video_url')->nullable();
            $table->text('footer_description')->nullable();
            $table->text('sponsor_text')->nullable();
            $table->json('event_info')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('landing_hero_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('landing_setting_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('lineup_artists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('image_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('batch_label')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->string('currency', 8)->default('IDR');
            $table->string('availability_label')->default('Available');
            $table->string('status')->default('available')->index();
            $table->string('purchase_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('merchandise_products', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kicker')->nullable();
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->string('currency', 8)->default('IDR');
            $table->text('description')->nullable();
            $table->string('thumbnail_path')->nullable();
            $table->string('thumbnail_alt')->nullable();
            $table->string('thumbnail_class')->nullable();
            $table->string('order_url')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('merchandise_product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchandise_product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->string('image_class')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('merchandise_product_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchandise_product_id')->constrained()->cascadeOnDelete();
            $table->string('text');
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('gallery_moments', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('username')->nullable();
            $table->string('image_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('sponsor_partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tier')->nullable()->index();
            $table->string('logo_path')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('contact_channels', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->string('type')->index();
            $table->string('value')->nullable();
            $table->string('url')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_channels');
        Schema::dropIfExists('sponsor_partners');
        Schema::dropIfExists('gallery_moments');
        Schema::dropIfExists('merchandise_product_features');
        Schema::dropIfExists('merchandise_product_images');
        Schema::dropIfExists('merchandise_products');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('lineup_artists');
        Schema::dropIfExists('landing_hero_images');
        Schema::dropIfExists('landing_settings');
    }
};
