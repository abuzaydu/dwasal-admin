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
        Schema::create('sale_return_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->unsignedBigInteger('sale_return_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->decimal('quantity', 15, 2);
            $table->decimal('retail_price', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('input_tax', 15, 2)->nullable()->default(0);
            $table->foreign('sale_return_id')->references('id')->on('sale_returns')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_return_items');
    }
};
