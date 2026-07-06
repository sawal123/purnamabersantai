<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->json('purchase_links')->nullable()->after('purchase_url');
        });

        DB::table('tickets')
            ->whereNotNull('purchase_url')
            ->orderBy('id')
            ->lazy()
            ->each(function (object $ticket): void {
                DB::table('tickets')
                    ->where('id', $ticket->id)
                    ->update([
                        'purchase_links' => json_encode([[
                            'label' => '',
                            'url' => $ticket->purchase_url,
                        ]], JSON_UNESCAPED_SLASHES),
                    ]);
            });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('purchase_links');
        });
    }
};
