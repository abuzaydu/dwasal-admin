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
        Schema::create('payroll_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->integer('days_work')->nullable()->default(0);
            $table->float('overtime_hrs')->nullable()->default(0);
            $table->integer('late')->nullable()->default(0);
            $table->integer('absences')->nullable()->default(0);
            $table->decimal('bonuses', 15,2)->nullable()->default(0);
            $table->decimal('penalty', 15,2)->nullable()->default(0);
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_temps');
    }
};
