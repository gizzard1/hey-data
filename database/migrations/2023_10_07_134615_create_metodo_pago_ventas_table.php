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
        Schema::create('metodo_pago_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnUpdate();
            $table->enum('Payment_method',['Efectivo','Card','Banorte','Puntos Recompensa','Descuento']);
            $table->enum('tipo',['Cantidad','Porcentaje']);
            $table->string('reference',50)->nullable()->default(null);
            $table->decimal('amount',20,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pago_ventas');
    }
};
