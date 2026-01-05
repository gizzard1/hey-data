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
        Schema::create('abono_propinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->constrained('citas')->nullable();
            $table->foreignId('venta_id')->constrained('ventas')->nullable();
            $table->foreignId('payment_method_id')->constrained('metodo_pagos')->nullable();
            $table->decimal('payed_qty',10,2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abono_propinas');
    }
};
