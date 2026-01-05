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
        Schema::create('excepcion_servicios', function (Blueprint $table) {
            $table->id();
            $table->enum('type_comission',['percent','qty'])->default('percent');
            $table->decimal('qty',10,2);
            $table->foreignId('comision_id')->constrained('comisions');
            $table->foreignId('servicio_id')->constrained('servicios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excepcion_servicios');
    }
};
