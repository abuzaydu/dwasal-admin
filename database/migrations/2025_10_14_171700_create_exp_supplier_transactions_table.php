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
        Schema::create('exp_supplier_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('supplier_id')->index(); 
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->boolean('is_ob')->default(false);
            $table->string('invoice_no')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('pv_no')->nullable();
            $table->decimal('payment', 15, 2)->nullable();
            $table->decimal('trans_invoice_amount', 15, 2)->nullable()->default(0);
            $table->decimal('trans_ob_amount', 15,2)->nullable()->default(0);
            $table->decimal('trans_credit_amount', 15,2)->nullable()->default(0);
            $table->boolean('is_utilized')->default(true);
            $table->string('payment_mode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('cn_no')->nullable();
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->string('reason')->nullable();
            $table->date('date');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->decimal('ob_paid', 15,2)->nullable()->default(0);
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_supplier_transactions');
    }
};
