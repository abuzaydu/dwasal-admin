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
        Schema::create('transfer_order_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transfer_order_temp_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('an_sale_id')->nullable();
            $table->decimal('source_stock', 15, 2);
            $table->decimal('destin_stock', 15, 2);
            $table->decimal('quantity', 15, 2);
            $table->decimal('source_unit_cost')->nullable()->default(0);
            $table->decimal('destin_unit_cost')->nullable()->default(0);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('transfer_order_item_temps');
    }
};
