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
        Schema::create('categoria_servicios_pivs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_servicio_id')->constrained('categoria_servicios');
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_servicios_pivs');
    }
};
