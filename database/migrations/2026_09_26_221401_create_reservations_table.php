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
Schema::create('reservations', function (Blueprint $table) {
    $table->id();

    $table->foreignId('user_id')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->foreignId('hotel_id')
        ->constrained('hotels')
        ->cascadeOnDelete();

    $table->date('reservation_date');
    $table->time('reservation_time');

    $table->unsignedInteger('number_of_guests');

    $table->enum('status', [
        'pending',
        'confirmed',
        'cancelled',
        'completed'
    ])->default('pending');

    $table->text('notes')->nullable();

    $table->timestamps();

    $table->index([
        'hotel_id',
        'reservation_date',
        'reservation_time'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
