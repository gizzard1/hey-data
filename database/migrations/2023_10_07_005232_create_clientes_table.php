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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',35);
            $table->string('last_name',35)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('edad',2)->nullable();
            $table->string('email',65)->unique()->nullable();
            $table->string('description',100)->nullable();
            $table->string('phone',15)->unique()->nullable();
            $table->boolean('want_custom_messages')->default(false)->nullable();
            $table->boolean('want_offers')->default(false)->nullable();
            $table->boolean('is_active')->default(true)->nullable();
            $table->integer('platform_id')->nullable();
            $table->foreignId('categoria_cliente_id')->nullable()->default(NULL)->constrained('categoria_clientes')->cascadeOnUpdate();
            $table->enum('sexo',['masculino','femenino','noBinario'])->nullable();
            $table->enum('procedencia',['instagram','facebook','google','tiktok','youtube','cliente','empleado','norma'])->nullable();
            $table->enum('sector',['sector1','sector2','sector3','sector4'])->nullable();
            $table->foreignId('salon_id')->constrained('salons')->default(isset(Auth::user()->salon->id));
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
