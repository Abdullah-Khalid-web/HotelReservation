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
Schema::create('hotel_tables', function (Blueprint $table) {
    $table->id();

    $table->foreignId('hotel_id')
        ->constrained('hotels')
        ->cascadeOnDelete();

    $table->string('table_number');
    $table->unsignedInteger('capacity');

    $table->enum('status', [
        'available',
        'reserved',
        'maintenance'
    ])->default('available');

    $table->timestamps();

    $table->unique([
        'hotel_id',
        'table_number'
    ]);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotel_tables');
    }
};
