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
        // add "shipped" to the order status enum
        Schema::table('orders', function (Blueprint $table) {
            // when altering enums you may need the doctrine/dbal package installed
            $table->enum('status', ['pending','processing','shipped','completed','cancelled'])
                  ->default('pending')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->enum('status', ['pending','processing','completed','cancelled'])
                  ->default('pending')
                  ->change();
        });
    }
};