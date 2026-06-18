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
    Schema::create('inventory_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
        $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
        $table->foreignId('store_user_id')->nullable()->constrained('store_users')->nullOnDelete();
        $table->enum('type', ['venta', 'entrada', 'ajuste', 'devolucion']);
        $table->integer('quantity');
        $table->integer('stock_before');
        $table->integer('stock_after');
        $table->string('note')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};
