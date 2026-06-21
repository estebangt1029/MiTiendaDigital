<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * MySQL no permite alterar un ENUM directamente con Schema::table(),
     * así que usamos SQL crudo para agregar 'compra' a los valores existentes.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory_logs MODIFY COLUMN type ENUM('venta', 'entrada', 'ajuste', 'devolucion', 'compra') NOT NULL");
    }

    public function down(): void
    {
        // Antes de revertir, aseguramos que no queden filas con 'compra' que rompan el enum viejo
        DB::statement("UPDATE inventory_logs SET type = 'entrada' WHERE type = 'compra'");
        DB::statement("ALTER TABLE inventory_logs MODIFY COLUMN type ENUM('venta', 'entrada', 'ajuste', 'devolucion') NOT NULL");
    }
};