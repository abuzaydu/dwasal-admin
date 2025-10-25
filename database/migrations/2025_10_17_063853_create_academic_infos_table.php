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
        Schema::create('academic_infos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('level');
            $table->string('institution');
            $table->string('title');
            $table->string('certificate_link')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_infos');
    }
};
