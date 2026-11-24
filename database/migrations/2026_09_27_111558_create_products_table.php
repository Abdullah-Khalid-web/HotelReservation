<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->foreignId('hotel_id')
                  ->constrained('hotels')
                  ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();

            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();

            $table->string('category')->nullable();          // "Starters", "Main Course", "Drinks"
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_available')->default(true);

            $table->integer('stock_quantity')->nullable();    // NULL = unlimited
            $table->integer('preparation_minutes')->default(15);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['hotel_id', 'is_available']);
            $table->index(['hotel_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
