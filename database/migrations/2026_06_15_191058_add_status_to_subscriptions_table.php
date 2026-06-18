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
    Schema::table('subscriptions', function (Blueprint $table) {
        $table->enum('status', ['pending', 'active', 'expired', 'cancelled'])
              ->default('pending')->after('active');
        $table->string('payment_proof')->nullable()->after('status'); // comprobante
        $table->text('notes')->nullable()->after('payment_proof');
        $table->timestamp('confirmed_at')->nullable()->after('notes');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            //
        });
    }
};
