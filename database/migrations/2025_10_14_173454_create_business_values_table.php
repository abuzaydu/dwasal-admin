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
        Schema::create('business_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->datetime('date');
            $table->decimal('total_cash', 15,2);
            $table->decimal('stock_value', 15,2);
            $table->decimal('cust_debts', 15,2);
            $table->decimal('supp_debts', 15,2);
            $table->decimal('other_debts', 15,2);
            $table->decimal('supp_credits', 15,2);
            $table->decimal('cust_credits', 15,2);
            $table->decimal('unpaid_expenses', 15, 2);
            $table->decimal('other_credits', 15,2);
            $table->decimal('paid_expenses',15,2);
            $table->decimal('discounts_made', 15,2);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_values');
    }
};
