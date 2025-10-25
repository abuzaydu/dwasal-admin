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
        Schema::create('daily_closing_stock_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->date('date');
            $table->decimal('start_qty', 15, 2)->nullable()->default(0);
            $table->decimal('purchase_qty', 15,2)->nullable()->default(0);
            $table->decimal('return_qty', 15,2)->nullable()->default(0);
            $table->decimal('sold_qty', 15, 2)->nullable()->default(0);
            $table->decimal('transfer_qty', 15, 2)->nullable()->default(0);
            $table->decimal('dam_qty', 15,2)->nullable()->default(0);
            $table->decimal('end_qty', 15,2)->nullable()->default(0);
            $table->decimal('start_value', 15, 2)->nullable()->default(0);
            $table->decimal('start_retail_value', 15, 2)->nullable()->default(0);
            $table->decimal('start_wholesale_value', 15, 2)->nullable()->default(0);
            $table->decimal('end_value', 15,2)->nullable()->default(0);
            $table->decimal('end_retail_value', 15, 2)->nullable()->default(0);
            $table->decimal('end_wholesale_value', 15, 2)->nullable()->default(0);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_closing_stock_values');
    }
};
