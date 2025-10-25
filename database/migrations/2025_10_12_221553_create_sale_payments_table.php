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
        Schema::create('sale_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('an_sale_id')->index();
            $table->unsignedBigInteger('trans_id')->index();
            $table->integer('receipt_no');
            $table->date('pay_date');
            $table->boolean('is_fresh_pay')->default(true);
            $table->decimal('amount', 15, 2);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 6)->default(1);
            $table->string('pay_mode');
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('cheque_no')->nullable();
            $table->string('cashier')->nullable();
            $table->datetime('cc_time')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->text('comments')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_payments');
    }
};
