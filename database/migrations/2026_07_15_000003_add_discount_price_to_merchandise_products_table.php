<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchandise_products', function (Blueprint $table): void {
            $table->unsignedInteger('discount_price')->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('merchandise_products', function (Blueprint $table): void {
            $table->dropColumn('discount_price');
        });
    }
};
