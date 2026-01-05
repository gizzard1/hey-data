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
        Schema::create('recompensas_cat_servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recompensas_servicio_id')->nullable()->constrained('recompensas_servicios')->cascadeOnUpdate();
            $table->foreignId('categoria_servicio_id')->nullable()->constrained('categoria_servicios')->cascadeOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recompensas_cat_servicios');
    }
};
