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
        Schema::table('cash_ins', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('cash_outs', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
            $table->boolean('is_loan_pay')->default(false);
        });

        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_account_id')->nullable();
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });
        
        Schema::table('expense_payments', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('rm_supplier_transactions', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('pm_supplier_transactions', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('prod_labour_costs', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('plc_payments', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('moh_costs', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });

        Schema::table('moh_cost_payments', function (Blueprint $table) {
            $table->boolean('is_added_to_ledger')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_ins', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('cash_outs', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
            $table->dropColumn('is_loan_pay');
        });
        Schema::table('customer_transactions', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('supplier_transactions', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('transaction_account_id');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('expense_payments', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('rm_supplier_transactions', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('pm_supplier_transactions', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });

        Schema::table('prod_labour_costs', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });

        Schema::table('plc_payments', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });

        Schema::table('moh_costs', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
        Schema::table('moh_cost_payments', function (Blueprint $table) {
            $table->dropColumn('is_added_to_ledger');
        });
    }
};
