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
        Schema::create('account_statements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('customer_transaction_id')->nullable();
            $table->unsignedBigInteger('supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('rm_supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('pm_supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('exp_supplier_transaction_id')->nullable();
            $table->unsignedBigInteger('account_transaction_id')->nullable();
            $table->unsignedBigInteger('cash_in_id')->nullable();
            $table->unsignedBigInteger('cash_out_id')->nullable();
            $table->unsignedBigInteger('petty_cash_id')->nullable();
            $table->unsignedBigInteger('plc_payment_id')->index()->nullable();
            $table->unsignedBigInteger('moh_cost_payment_id')->index()->nullable();
            $table->unsignedBigInteger('payroll_deduction_payment_id')->nullable();
            $table->unsignedBigInteger('employee_loan_id')->nullable();
            $table->datetime('date');
            $table->decimal('debit', 15,2)->nullable()->default(0);
            $table->decimal('credit', 15,2)->nullable()->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->string('description')->nullable();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_statements');
    }
};
