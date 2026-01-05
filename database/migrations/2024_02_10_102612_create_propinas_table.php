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
        Schema::create('propinas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cita_id')->nullable()->constrained('citas')->cascadeOnUpdate();
            $table->foreignId('empleado_id')->nullable()->constrained('empleados')->cascadeOnUpdate();
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->cascadeOnUpdate();
            $table->enum('Payment_method',['Efectivo','Card','Banorte']);
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
        Schema::dropIfExists('propinas');
    }
};
