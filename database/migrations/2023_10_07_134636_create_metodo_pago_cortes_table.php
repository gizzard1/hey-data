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
        Schema::create('metodo_pago_cortes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_corte_id')->constrained('caja_cortes')->cascadeOnUpdate();
            $table->string('payment_method',50)->nullable()->default(null);
            $table->decimal('qty',20,2);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pago_cortes');
    }
};
