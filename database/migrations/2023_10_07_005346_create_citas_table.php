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
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start')->nullable()->default(null);
            $table->dateTime('end')->nullable()->default(null);
            $table->decimal('generated_points',10,2)->nullable();
            $table->enum('date_status',['Agendada','Cancelada','Pagada'])->default('Agendada');
            $table->foreignId('customer_id')->constrained('clientes')->cascadeOnUpdate();            $table->foreignId('salon_id')->constrained('salons')->default(isset(Auth::user()->salon->id));
            $table->foreignId('user_id')->constrained('users')->cascadeOnUpdate();
            $table->decimal('total',10,2);
            $table->decimal('disccount',10,2);
            $table->boolean('remember');
            $table->string('motivoCancelacion',200)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
