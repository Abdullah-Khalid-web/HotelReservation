<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
Schema::create('reservation_items', function (Blueprint $table) {
    $table->id();

    $table->foreignId('reservation_id')
        ->constrained('reservations')
        ->cascadeOnDelete();

    $table->foreignId('table_id')
        ->constrained('hotel_tables')
        ->cascadeOnDelete();

    $table->timestamps();

    $table->unique([
        'reservation_id',
        'table_id'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservation_items');
    }
};
