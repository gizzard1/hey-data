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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->decimal('total',10,2);
            $table->integer('items');
            $table->decimal('disccount',10,2);
            $table->decimal('generated_points',10,2)->nullable()->default(NULL);
            $table->enum('sale_status',['Pagado','Devuelto','Pendiente'])->default('Pagado');
            $table->foreignId('customer_id')->constrained('clientes')->cascadeOnUpdate();
            $table->foreignId('user_id')->constrained('users');
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
        Schema::dropIfExists('ventas');
    }
};
