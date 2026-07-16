<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rundown_map_categories')) {
            Schema::create('rundown_map_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->unsignedInteger('sort_order')->default(0)->index();
                $table->boolean('is_active')->default(true)->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        $now = now();

        foreach ([
            ['name' => 'Rundown', 'slug' => 'rundown', 'sort_order' => 10],
            ['name' => 'Map', 'slug' => 'map', 'sort_order' => 20],
        ] as $category) {
            DB::table('rundown_map_categories')->updateOrInsert(
                ['slug' => $category['slug']],
                [
                    'name' => $category['name'],
                    'sort_order' => $category['sort_order'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        Schema::table('rundown_maps', function (Blueprint $table) {
            if (! Schema::hasColumn('rundown_maps', 'category_id')) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('rundown_map_categories')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('rundown_maps', 'title')) {
                $table->string('title')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('rundown_maps', 'image_path')) {
                $table->string('image_path')->nullable()->after('title');
            }

            if (! Schema::hasColumn('rundown_maps', 'date')) {
                $table->date('date')->nullable()->after('image_path')->index();
            }

            if (! Schema::hasColumn('rundown_maps', 'description')) {
                $table->text('description')->nullable()->after('date');
            }
        });

        $rundownCategoryId = DB::table('rundown_map_categories')->where('slug', 'rundown')->value('id');

        if ($rundownCategoryId && Schema::hasTable('rundown_map_images')) {
            $existingImagePaths = DB::table('rundown_maps')
                ->whereNotNull('image_path')
                ->pluck('image_path')
                ->all();

            DB::table('rundown_map_images')
                ->orderBy('id')
                ->chunkById(100, function ($images) use ($existingImagePaths, $now, $rundownCategoryId) {
                    $knownPaths = collect($existingImagePaths);

                    foreach ($images as $image) {
                        if ($knownPaths->contains($image->image_path)) {
                            continue;
                        }

                        $parent = DB::table('rundown_maps')->where('id', $image->rundown_map_id)->first();
                        $year = (int) ($parent->tahun ?? now()->year);

                        DB::table('rundown_maps')->insert([
                            'category_id' => $rundownCategoryId,
                            'title' => $image->name ?: 'Rundown '.$year,
                            'image_path' => $image->image_path,
                            'date' => $year.'-01-01',
                            'description' => null,
                            'tahun' => $year,
                            'google_map' => null,
                            'is_active' => (bool) ($image->is_active ?? true),
                            'deleted_at' => $image->deleted_at ?? null,
                            'created_at' => $image->created_at ?? $now,
                            'updated_at' => $image->updated_at ?? $now,
                        ]);

                        $knownPaths->push($image->image_path);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('rundown_maps', function (Blueprint $table) {
            foreach (['description', 'date', 'image_path', 'title'] as $column) {
                if (Schema::hasColumn('rundown_maps', $column)) {
                    $table->dropColumn($column);
                }
            }

            if (Schema::hasColumn('rundown_maps', 'category_id')) {
                $table->dropConstrainedForeignId('category_id');
            }
        });

        Schema::dropIfExists('rundown_map_categories');
    }
};
