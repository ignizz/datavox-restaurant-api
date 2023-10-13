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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('development_id');
            $table->unsignedBigInteger('property_type_id');
            $table->unsignedBigInteger('user_id')->nullable()->comment("El usuario que es dueño de la propiedad/inmueble");
            $table->timestamp('record')->nullable();
            // $table->timestamp('record')->nullable()->default(time());
            $table->boolean('blocking');
            $table->string('name');
            $table->timestamps();
            $table->foreign('development_id')->references('id')->on('developments');
            $table->foreign('property_type_id')->references('id')->on('property_types');
            // $table->timestamp('added_on')->nullable()->default(time());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
