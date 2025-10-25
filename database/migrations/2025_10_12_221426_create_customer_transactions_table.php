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
        Schema::create('customer_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('an_sale_id')->nullable();
            $table->date('date');
            $table->integer('invoice_no')->nullable();
            $table->boolean('is_ob')->default(false);
            $table->unsignedBigInteger('cash_out_id')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->integer('receipt_no')->nullable();
            $table->decimal('payment', 15, 2)->nullable();
            $table->decimal('trans_invoice_amount', 15, 2)->nullable()->default(0);
            $table->decimal('trans_ob_amount', 15,2)->nullable()->default(0);
            $table->decimal('trans_credit_amount', 15,2)->nullable()->default(0);
            $table->boolean('is_utilized')->default(true);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 6)->default(1); 
            $table->string('payment_mode')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('cashier')->nullable();
            $table->datetime('cc_time')->nullable();
            $table->string('cn_no')->nullable();
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->decimal('ob_paid', 15,2)->nullable()->default(0);
            $table->boolean('is_refund')->default(false);
            $table->integer('refund_no')->nullable();
            $table->decimal('refund_amt', 15, 2)->nullable();
            $table->string('status')->nullable()->default('Awaiting for Approval');
            $table->text('remarks')->nullable();
            $table->string('approved_by')->nullable();
            $table->datetime('approved_time')->nullable();
            $table->string('confirmed_by')->nullable();
            $table->string('confirm_time')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_transactions');
    }
};
