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
        Schema::create('recompensas_cat_clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->nullable()->constrained('programa_recompensas')->cascadeOnUpdate();
            $table->foreignId('client_category_id')->nullable()->constrained('categoria_clientes')->cascadeOnUpdate();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recompensas_cat_clientes');
    }
};
