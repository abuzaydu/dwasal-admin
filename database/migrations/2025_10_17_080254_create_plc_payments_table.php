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
        Schema::create('plc_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('pv_no')->nullable();
            $table->datetime('pay_date');
            $table->decimal('amount', 15, 2);
            $table->decimal('utilized_amt', 15,2)->nullable()->default(0);
            $table->boolean('is_utilized')->default(false);
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
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plc_payments');
    }
};
