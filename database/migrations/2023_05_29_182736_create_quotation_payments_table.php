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
        Schema::create('quotation_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->double('amount', 10, 2); // monto del pago
            $table->date('date'); // fecha del pago progamado
            $table->string('payment_method')->nullable()->comment("el método de pago puede ser transferencia, cash, cheque, etc.");
            $table->string('voucher')->nullable()->comment("imagen del voucher");
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->foreign('quotation_id')->references('id')->on('quotations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotation_payments');
    }
};
