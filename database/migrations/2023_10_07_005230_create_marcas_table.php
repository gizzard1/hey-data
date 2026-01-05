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
        Schema::create('marcas', function (Blueprint $table) {
            $table->id();
            $table->string('name',55)->unique();
            $table->string('contact_name',55)->nullable()->default(null);
            $table->string('phone_number',55)->nullable()->default(null);
            $table->string('email',55)->unique()->nullable();
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
        Schema::dropIfExists('marcas');
    }
};
