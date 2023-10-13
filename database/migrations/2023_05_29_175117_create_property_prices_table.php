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
        Schema::create('property_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->double('amount', 10, 2);
            // $table->unsignedBigInteger('type_price')->nullable()->default(1)->comment("El tipo de precio, ya sea el original, con descuento o el valor después de cierto tiempo");
            $table->enum('type_price', PropertyPrice::PRICE_TYPES)->default(1)->comment("El tipo de precio, ya sea el original, con descuento o el valor después de cierto tiempo");
            $table->timestamps();
            $table->softDeletes($column = 'deleted_at', $precision = 0);
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_prices');
    }
};
