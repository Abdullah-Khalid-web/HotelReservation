<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hotel_tables', function (Blueprint $table) {
            $table->unique(
                ['hotel_id', 'table_number'],
                'unique_table_number_per_hotel'
            );
        });
    }

    public function down(): void
    {
        Schema::table('hotel_tables', function (Blueprint $table) {
            $table->dropUnique('unique_table_number_per_hotel');
        });
    }
};
