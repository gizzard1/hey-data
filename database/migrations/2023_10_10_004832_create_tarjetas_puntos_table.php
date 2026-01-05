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
        Schema::create('tarjetas_puntos', function (Blueprint $table) {
            $table->id();
            $table->string('intern_barcode',100)->nullable()->default(null);
            $table->decimal('balance',20,2)->nullable()->default(null);
            $table->foreignId('cliente_id')->nullable()->unique()->default(null)->constrained('clientes')->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarjetas_puntos');
    }
};
