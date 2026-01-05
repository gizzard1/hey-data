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
        Schema::create('detalle_compras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_id')->constrained('compras')->cascadeOnUpdate();
            $table->foreignId('product_id')->constrained('productos')->cascadeOnUpdate();
            $table->decimal('gross_price',10,2);
            $table->decimal('cost',10,2);
            $table->integer('quantity');
            $table->integer('recived_quantity')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compras');
    }
};
