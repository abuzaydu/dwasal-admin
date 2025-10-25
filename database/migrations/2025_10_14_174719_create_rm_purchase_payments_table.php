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
        Schema::create('rm_purchase_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('rm_purchase_id')->index();
            $table->unsignedBigInteger('rm_trans_id')->index();
            $table->string('pv_no')->nullable();
            $table->datetime('pay_date');
            $table->decimal('amount', 15, 2);
            $table->string('currency')->nullable();
            $table->string('defcurr')->nullable();
            $table->decimal('ex_rate', 15, 5)->default(1);
            $table->string('pay_mode');
            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('cheque_no')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->text('comments')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('rm_purchase_id')->references('id')->on('rm_purchases')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_purchase_payments');
    }
};
