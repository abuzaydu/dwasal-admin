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
        Schema::create('next_of_kin', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('f_name');
            $table->string('m_name')->nullable();
            $table->string('l_name');
            $table->string('relationship');
            $table->string('occupation');
            $table->string('f_phone');
            $table->string('s_phone')->nullable();
            $table->string('address')->nullable();
            $table->string('residence');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('next_of_kin');
    }
};
