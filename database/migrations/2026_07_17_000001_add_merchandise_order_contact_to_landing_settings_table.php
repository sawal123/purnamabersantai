<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('landing_settings', 'merchandise_order_contact')) {
                $table->string('merchandise_order_contact')->nullable()->after('event_info');
            }
        });
    }

    public function down(): void
    {
        Schema::table('landing_settings', function (Blueprint $table) {
            if (Schema::hasColumn('landing_settings', 'merchandise_order_contact')) {
                $table->dropColumn('merchandise_order_contact');
            }
        });
    }
};
