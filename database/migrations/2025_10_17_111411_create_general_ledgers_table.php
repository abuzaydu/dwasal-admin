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
        Schema::create('general_ledgers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('transaction_account_id')->index();
            $table->unsignedBigInteger('customer_transaction_id')->nullable();
            $table->unsignedBigInteger('supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('rm_supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('pm_supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->unsignedBigInteger('expense_payment_id')->nullable();
            $table->unsignedBigInteger('cash_in_id')->nullable();
            $table->unsignedBigInteger('cash_out_id')->nullable();
            $table->unsignedBigInteger('prod_labour_cost_id')->nullable();
            $table->unsignedBigInteger('plc_payment_id')->nullable();
            $table->unsignedBigInteger('moh_cost_id')->nullable();
            $table->unsignedBigInteger('moh_cost_payment_id')->nullable();
            $table->unsignedBigInteger('payroll_deduction_id')->nullable();
            $table->unsignedBigInteger('payroll_deduction_payment_id')->nullable();
            $table->unsignedBigInteger('employee_loan_id')->nullable();
            $table->unsignedBigInteger('employee_loan_return_id')->nullable();
            $table->date('date');
            $table->string('transaction_description');
            $table->string('reference')->nullable();
            $table->decimal('debit_amount', 15,2)->nullable()->default(0);
            $table->decimal('credit_amount', 15,2)->nullable()->default(0);
            $table->foreign('transaction_account_id')->references('id')->on('transaction_accounts');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('general_ledgers');
    }
};
