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
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->integer('qty');
            $table->decimal('cost',10,2)->nullable()->default(null);
            $table->enum('iva',['0.16','0.08','0']);
            $table->foreignId('producto_id')->constrained('productos')->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->foreignId('salon_id')->constrained('salons')->cascadeOnUpdate();
            $table->foreignId('marca_id')->constrained('marcas')->cascadeOnUpdate();
            $table->string('description',100)->nullable();
            $table->string('folio_fiscal',36)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
