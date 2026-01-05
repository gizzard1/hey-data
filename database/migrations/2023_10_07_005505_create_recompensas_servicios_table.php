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
        Schema::create('recompensas_servicios', function (Blueprint $table) {
            $table->id();
            $table->boolean('to_all')->nullable()->default(false);
            $table->decimal('percent',10,2);
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
        Schema::dropIfExists('recompensas_servicios');
    }
};
