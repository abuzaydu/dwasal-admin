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
        Schema::create('expense_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('expense_id')->index();
            $table->unsignedBigInteger('trans_id')->index()->nullable();
            $table->string('pv_no')->nullable();
            $table->date('pay_date');
            $table->decimal('amount', 15, 2);
            $table->string('pay_mode');
            $table->string('reference')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_payments');
    }
};
