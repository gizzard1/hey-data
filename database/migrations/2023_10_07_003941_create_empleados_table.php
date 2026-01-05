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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('first_name',80);
            $table->string('last_name',80)->nullable();
            $table->string('email',80)->unique()->nullable();
            $table->string('phone_number',15)->nullable()->unique();
            $table->date('birth_date')->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->foreignId('user_id')->nullable()->unique()->constrained('users');
            $table->foreignId('salon_id')->default(isset(Auth::user()->salon->id))->constrained('salons');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
