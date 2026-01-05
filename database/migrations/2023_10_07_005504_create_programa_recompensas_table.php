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
        Schema::create('programa_recompensas', function (Blueprint $table) {
            $table->id();
            $table->string('name',100);
            $table->string('description',300)->nullable();
            $table->decimal('required_points',10,2);
            $table->dateTime('expiration_date');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programa_recompensas');
    }
};
