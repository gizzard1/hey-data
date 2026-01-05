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
        Schema::create('excepcion_cat_servicio_pivs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comision_id')->constrained('comisions');
            $table->foreignId('excepcion_cat_servicio_id')->constrained('excepcion_cat_servicios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excepcion_cat_servicio_pivs');
    }
};
