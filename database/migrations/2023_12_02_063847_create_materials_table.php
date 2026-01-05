<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')->constrained('asignacion_servicios')->nullable();
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnUpdate();
            $table->integer('qty')->default(1)->nullable();
            $table->decimal('sale_price',10,2)->nullable();
            $table->foreignId('salon_id')->constrained('salons');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('empleado_id')->constrained('empleados');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
