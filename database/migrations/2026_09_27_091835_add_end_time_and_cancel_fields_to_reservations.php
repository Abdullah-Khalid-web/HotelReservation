<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->time('reservation_end_time')->nullable()->after('reservation_time');
            $table->string('cancellation_reason')->nullable()->after('status');
            $table->time('suggested_time')->nullable()->after('cancellation_reason');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'reservation_end_time',
                'cancellation_reason',
                'suggested_time',
            ]);
        });
    }
};
