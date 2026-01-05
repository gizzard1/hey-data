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
        Schema::create('excepcion_producto_pivs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comision_id')->constrained('comisions');
            $table->foreignId('excepcion_producto_id')->constrained('excepcion_productos');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excepcion_producto_pivs');
    }
};
