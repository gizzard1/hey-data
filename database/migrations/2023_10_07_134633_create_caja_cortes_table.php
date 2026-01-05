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
        Schema::create('caja_cortes', function (Blueprint $table) {
            $table->id();
            $table->string('description',100)->nullable();
            $table->decimal('total_bruto',20,2);
            $table->decimal('total_neto',20,2);
            $table->decimal('ganancia',20,2);
            $table->decimal('total_ventas',20,2);
            $table->decimal('total_servicios',20,2);
            $table->decimal('total_cash',20,2);
            $table->decimal('total_NF',20,2);
            $table->decimal('total_points',20,2);
            $table->decimal('tips',20,2);
            $table->decimal('comissions',20,2);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja_cortes');
    }
};
