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
        Schema::create('etiquetas_citas_pivs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('etiquetas_cita_id')->constrained('etiquetas_citas');
            $table->foreignId('cita_id')->constrained('citas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('etiquetas_citas_pivs');
    }
};
