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
        Schema::create('entrega_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clientes')->cascadeOnUpdate();
            $table->foreignId('delivery_id')->constrained('entregas')->cascadeOnUpdate();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrega_clientes');
    }
};
