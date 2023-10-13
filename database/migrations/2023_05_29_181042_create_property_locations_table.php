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
        Schema::create('property_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('property_id');
            $table->string('coordinates')->nullable(); // calle
            $table->string('northern_adjoining')->nullable(); // colindancia norte
            $table->string('southern_adjoining')->nullable(); // colindancia sur
            $table->string('manzana')->nullable(); // manzana (no supe como se dice en ingles)
            $table->string('lote')->nullable(); // lote
            $table->string('northeast')->nullable(); // noreste
            $table->string('northwest')->nullable(); // noroeste
            $table->string('southeast')->nullable(); // sureste
            $table->string('southwest')->nullable(); // suroeste
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_locations');
    }
};
