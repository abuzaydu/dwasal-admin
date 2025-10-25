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
        Schema::create('employee_loan_returns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_loan_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->date('return_date');
            $table->decimal('amount', 15,2);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('employee_loan_id')->references('id')->on('employee_loans');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loan_returns');
    }
};
