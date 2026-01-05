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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('name',50);
            $table->enum('type_product',['simple','variable']);
            $table->decimal('gross_price',10,2);
            $table->decimal('disccount_price',10,2)->nullable()->default(null);
            $table->decimal('cost',10,2)->nullable();
            $table->enum('unit_type',['Unidad','Mililitro','Ampolleta','Artículo','Onza','Gramo','Envase'])->default('Unidad');
            $table->integer('stock_qty')->nullable();
            $table->integer('min_stock')->nullable();
            $table->string('sku',80)->nullable();
            $table->string('intern_sku',80)->nullable();
            $table->string('description',1000)->nullable();
            $table->decimal('reward_points',10,2)->nullable();
            $table->enum('status',['publish','pending','draft']);
            $table->enum('visibility',['visible','hide']);
            $table->enum('stock_status',['instock','outofstock','onbackorder']);
            $table->tinyInteger('manage_stock')->default(1);
            $table->integer('platform_id')->nullable();
            $table->foreignId('brand_id')->nullable()->constrained('marcas')->cascadeOnUpdate();
            $table->foreignId('salon_id')->constrained('salons')->default(isset(Auth::user()->salon->id));
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
