<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            // Allow guest reservations (no logged-in user)
            $table->foreignId('user_id')->nullable()->change();

            // Guest identity
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_phone', 30)->nullable()->after('guest_name');
            $table->string('guest_email')->nullable()->after('guest_phone');

            // Optional: a public token so guests can view/cancel without login
            $table->string('guest_token', 64)->nullable()->unique()->after('guest_email');

            // Index for linking on register
            $table->index('guest_phone');
            $table->index('guest_email');
        });

        // Same for pre_orders if guests can order
        Schema::table('pre_orders', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
            $table->string('guest_token', 64)->nullable()->unique()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropIndex(['guest_phone']);
            $table->dropIndex(['guest_email']);
            $table->dropUnique(['guest_token']);
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_email', 'guest_token']);
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('pre_orders', function (Blueprint $table) {
            $table->dropUnique(['guest_token']);
            $table->dropColumn('guest_token');
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }
};
