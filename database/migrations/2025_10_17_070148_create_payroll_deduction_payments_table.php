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
        Schema::create('payroll_deduction_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('payroll_deduction_id')->index();
            $table->date('pay_date');
            $table->decimal('amount_paid', 15,2);
            $table->string('pay_mode');
            $table->string('reference')->nullable();
            $table->boolean('is_added_to_ledger')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('payroll_deduction_id')->references('id')->on('payroll_deductions');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_deduction_payments');
    }
};
