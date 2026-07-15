<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tickets')) {
            return;
        }

        DB::table('tickets')->orderBy('id')->get(['id', 'status'])->each(function ($ticket): void {
            DB::table('tickets')
                ->where('id', $ticket->id)
                ->update([
                    'availability_label' => match ((string) $ticket->status) {
                        'limited' => 'Limited Seat',
                        'sold_out' => 'Sold Out',
                        'coming_soon' => 'Coming Soon',
                        default => 'Available',
                    },
                ]);
        });
    }

    public function down(): void
    {
        //
    }
};
