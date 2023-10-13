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
        Schema::create('property_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('street')->nullable(); // calle
            $table->string('num_ext')->nullable(); // número exterior
            $table->string('num_int')->nullable(); // número interior
            $table->string('neighborhood')->nullable(); // colonia
            $table->string('zip_code')->nullable(); // código postal
            $table->string('city')->nullable(); // ciudad
            $table->string('state')->nullable(); // estado
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_addresses');
    }
};
