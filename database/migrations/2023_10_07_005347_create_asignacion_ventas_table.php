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
        Schema::create('asignacion_ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('selected_item')->constrained('productos')->cascadeOnUpdate();
            $table->foreignId('cita_id')->nullable()->constrained('citas');
            $table->foreignId('venta_id')->nullable()->constrained('ventas');
            $table->foreignId('empleado_id')->constrained('empleados')->cascadeOnUpdate();
            $table->integer('quantity');
            $table->decimal('disccount_percent',10,2)->nullable()->default(null);
            $table->decimal('generated_points',10,2)->nullable()->default(null);
            $table->decimal('current_price',10,2);
            $table->decimal('disccount_price',10,2)->nullable()->default(null);
            $table->decimal('comission',10,2)->nullable()->default(NULL);
            $table->enum('iva',['0.16','0.08','0']);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_ventas');
    }
};
