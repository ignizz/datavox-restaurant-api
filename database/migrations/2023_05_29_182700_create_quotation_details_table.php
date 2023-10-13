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
        Schema::create('quotation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quotation_id');
            $table->double('amount', 10, 2); // precio cotizado
            $table->double('down_payment', 10, 2); // enganche
            $table->double('down_payment_percentage', 4, 2); // porcentaje del enganche conforme al precio establecido
            $table->double('discount', 10, 2)->default(0); // descuento a precio original
            $table->unsignedSmallInteger('payment_number', $autoIncrement = false, $unsigned = true, 4)->nullable(); // número de pagos
            $table->double('payment_amount', 10, 2)->nullable(); // monto por pago
            $table->string('payment_plan')->nullable()->comment("plan de pago (mensual, quincenal o anual)"); // plan de pago (mensual, quincenal o anual)
            $table->string('payment_method')->nullable()->comment("el método de pago aquí hace referencia a si será a Crédito o Contado");
            $table->date('date_start'); // fecha del primer pago progamado
            $table->date('date_end'); // fecha del último pago programado
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
        Schema::dropIfExists('quotation_details');
    }
};
