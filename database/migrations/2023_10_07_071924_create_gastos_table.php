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
        Schema::create('gastos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type',['Acreditable','No acreditable']);
            $table->string('description',255);
            $table->decimal('total',10,2);
            $table->enum('iva',['0.16','0.8','0']);
            $table->foreignId('salon_id')->constrained('salons')->default(isset(Auth::user()->salon->id));
            $table->dateTime('date')->default(null);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gastos');
    }
};
