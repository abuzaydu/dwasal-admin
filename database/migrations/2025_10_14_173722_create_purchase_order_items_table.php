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
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id')->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('qty', 15,2)->default(1);
            $table->decimal('received_qty', 15,2)->nullable()->default(0);
            $table->decimal('unit_cost', 15,2);
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
