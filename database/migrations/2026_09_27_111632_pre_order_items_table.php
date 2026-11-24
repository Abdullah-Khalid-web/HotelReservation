<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pre_order_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pre_order_id')
                  ->constrained('pre_orders')
                  ->cascadeOnDelete();

            // Product can't be deleted if it's in an order — soft delete on product
            $table->foreignId('product_id')
                  ->constrained('products')
                  ->restrictOnDelete();

            $table->integer('quantity')->default(1);

            // SNAPSHOTS — price at time of order (product price may change later)
            $table->string('product_name');                    // snapshot name
            $table->decimal('unit_price', 10, 2);              // snapshot price
            $table->decimal('line_total', 10, 2);              // unit_price * quantity

            $table->text('notes')->nullable();                 // "No onions", "Extra spicy"

            $table->timestamps();

            $table->index('pre_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pre_order_items');
    }
};
