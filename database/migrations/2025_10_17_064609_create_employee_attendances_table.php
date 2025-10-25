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
        Schema::create('employee_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('employee_id');
            $table->datetime('start_of_day')->nullable();
            $table->datetime('end_of_day')->nullable();
            $table->string('status')->default('Absent');
            $table->boolean('is_fullday')->default(true);
            $table->boolean('is_present')->default(false);
            $table->boolean('is_late')->default(false);
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_attendances');
    }
};
