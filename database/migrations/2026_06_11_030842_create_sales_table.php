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
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
        $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
        $table->foreignId('store_user_id')->nullable()->constrained('store_users')->nullOnDelete();
        $table->enum('type', ['contado', 'fiado'])->default('contado');
        $table->decimal('total', 10, 2);
        $table->decimal('paid', 10, 2)->default(0);
        $table->decimal('debt', 10, 2)->default(0);
        $table->enum('status', ['pagada', 'pendiente', 'parcial'])->default('pagada');
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
