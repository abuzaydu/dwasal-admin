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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('m_payroll_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->date('month');
            $table->string('payid')->unique();
            $table->integer('days_work');
            $table->float('overtime_hrs')->nullable()->default(0);
            $table->integer('late')->nullable()->default(0);
            $table->integer('absences')->nullable()->default(0);
            $table->decimal('bonuses', 15,2)->nullable()->default(0);
            $table->decimal('penalty', 15,2)->nullable()->default(0);
            $table->text('note')->nullable();
            $table->foreign('m_payroll_id')->references('id')->on('m_payrolls');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
