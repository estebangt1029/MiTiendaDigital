<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->foreignId('store_user_id')->nullable()->constrained('store_users')->nullOnDelete();
            // 'contado' = se paga todo de una; 'credito' = queda debiendo al proveedor (espejo de 'fiado')
            $table->enum('type', ['contado', 'credito'])->default('contado');
            $table->decimal('total', 10, 2);
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('debt', 10, 2)->default(0);
            $table->enum('status', ['pagada', 'pendiente', 'parcial'])->default('pagada');
            // Si la marcas true, los costos de los productos comprados se actualizan con el costo de esta compra
            $table->boolean('update_product_cost')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};