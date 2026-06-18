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
    Schema::create('payments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
        $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete();
        $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
        $table->foreignId('store_user_id')->nullable()->constrained('store_users')->nullOnDelete();
        $table->decimal('amount', 10, 2);
        $table->enum('method', ['efectivo', 'transferencia', 'nequi', 'daviplata', 'otro'])->default('efectivo');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
