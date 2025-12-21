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
        Schema::create('vendor_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('vendor_id')->index(); 
            $table->unsignedBigInteger('part_purchase_id')->index()->nullable();
            $table->boolean('is_ob')->default(false);
            $table->unsignedBigInteger('cash_in_id')->nullable();
            $table->string('invoice_no')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('pv_no')->nullable();
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
            $table->string('cn_no')->nullable();
            $table->decimal('adjustment', 15, 2)->nullable();
            $table->string('reason')->nullable();
            $table->date('date');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->decimal('ob_paid', 15,2)->nullable()->default(0);
            $table->foreign('part_purchase_id')->references('id')->on('part_purchases');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_transactions');
    }
};
