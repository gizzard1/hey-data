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
        Schema::create('entregas', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',35);
            $table->string('last_name',45)->nullable();
            $table->string('company',60)->nullable(); 
            $table->string('primary_address')->nullable(); 
            $table->string('secondary_address')->nullable(); 
            $table->string('city',50)->nullable(); 
            $table->string('state',50)->nullable(); 
            $table->string('postcode',10)->nullable(); 
            $table->string('email',55)->nullable(); 
            $table->string('phone',15)->nullable(); 
            $table->string('country',40)->nullable();
            $table->enum('type',['billing','shipping'])->default('shipping');
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
        Schema::dropIfExists('entregas');
    }
};
