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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->string('series')->nullable(); // Serie
            $table->string('number')->nullable(); // Número
            $table->string('voucher_type')->nullable(); // Tipo de comprobante
            $table->string('currency')->nullable(); // Moneda
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropColumn(['series', 'number', 'voucher_type', 'currency']);
        });
    }
};
