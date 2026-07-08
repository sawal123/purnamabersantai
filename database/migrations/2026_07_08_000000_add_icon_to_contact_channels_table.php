<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contact_channels', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('label');
        });

        DB::table('contact_channels')
            ->whereNull('icon')
            ->orderBy('id')
            ->get()
            ->each(function (object $channel) {
                DB::table('contact_channels')
                    ->where('id', $channel->id)
                    ->update([
                        'icon' => match ($channel->type) {
                            'whatsapp', 'phone' => 'whatsapp',
                            'email' => 'email',
                            'instagram' => 'instagram',
                            'tiktok' => 'tiktok',
                            'website' => 'website',
                            default => 'chat-bubble',
                        },
                    ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_channels', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
