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
        Schema::create('walogs', function (Blueprint $table) {
            $table->id();
            $table->string('uid',100)->nullable();
            $table->boolean('sent')->default(true)->nullable();
            $table->boolean('answered')->nullable();
            $table->foreignId('cita_id')->constrained('citas')->nullable();
            $table->foreignId('venta_id')->constrained('ventas')->nullable();
            $table->enum('type',['confirmar_cita','enviar_ticket','ofrecer_producto','ofrecer_servicio','enviar_encuesta','error']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('walogs');
    }
};
