<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchandise_products', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0)->after('currency');
            $table->json('size_options')->nullable()->after('description');
            $table->json('color_options')->nullable()->after('size_options');
        });
    }

    public function down(): void
    {
        Schema::table('merchandise_products', function (Blueprint $table) {
            $table->dropColumn(['stock_quantity', 'size_options', 'color_options']);
        });
    }
};
