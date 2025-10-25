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
        Schema::create('transfer_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('transfer_order_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('stock_id')->index()->nullable();
            $table->decimal('source_stock', 15, 2);
            $table->decimal('destin_stock', 15, 2);
            $table->decimal('quantity', 15, 2)->nullable()->default(0);
            $table->decimal('req_qty', 15, 2)->nullable()->default(0);
            $table->decimal('rec_qty', 15,2)->nullable();
            $table->decimal('source_unit_cost',15, 2)->nullable()->default(0);
            $table->decimal('destin_unit_cost', 15, 2)->nullable()->default(0);
            $table->decimal('source_unit_price', 15, 2)->nullable()->default(0);
            $table->boolean('is_cancelled')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('transfer_order_id')->references('id')->on('transfer_orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_order_items');
    }
};
