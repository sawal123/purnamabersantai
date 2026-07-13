<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('merchandise_product_categories')) {
            Schema::create('merchandise_product_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('merchandise_products') && ! Schema::hasColumn('merchandise_products', 'merchandise_product_category_id')) {
            Schema::table('merchandise_products', function (Blueprint $table) {
                $table->foreignId('merchandise_product_category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('merchandise_product_categories')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasTable('merchandise_products') || ! Schema::hasColumn('merchandise_products', 'kicker')) {
            return;
        }

        $now = now();
        $kickers = DB::table('merchandise_products')
            ->whereNotNull('kicker')
            ->where('kicker', '<>', '')
            ->distinct()
            ->pluck('kicker')
            ->filter(fn ($kicker) => is_string($kicker) && trim($kicker) !== '')
            ->values();

        foreach ($kickers as $index => $kicker) {
            $name = trim((string) $kicker);
            $baseSlug = Str::slug($name) ?: 'category';
            $slug = $baseSlug;
            $counter = 2;

            while (
                DB::table('merchandise_product_categories')
                    ->where('slug', $slug)
                    ->where('name', '<>', $name)
                    ->exists()
            ) {
                $slug = $baseSlug.'-'.$counter;
                $counter++;
            }

            $categoryId = DB::table('merchandise_product_categories')
                ->where('name', $name)
                ->value('id');

            if (! $categoryId) {
                $categoryId = DB::table('merchandise_product_categories')->insertGetId([
                    'name' => $name,
                    'slug' => $slug,
                    'sort_order' => $index + 1,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            DB::table('merchandise_products')
                ->where('kicker', $name)
                ->whereNull('merchandise_product_category_id')
                ->update([
                    'merchandise_product_category_id' => $categoryId,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('merchandise_products') && Schema::hasColumn('merchandise_products', 'merchandise_product_category_id')) {
            Schema::table('merchandise_products', function (Blueprint $table) {
                $table->dropConstrainedForeignId('merchandise_product_category_id');
            });
        }

        Schema::dropIfExists('merchandise_product_categories');
    }
};
