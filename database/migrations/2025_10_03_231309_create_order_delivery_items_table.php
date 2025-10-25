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
        Schema::create('order_delivery_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_delivery_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->string('uom');
            $table->decimal('quantity');
            $table->foreign('order_delivery_id')->references('id')->on('order_deliveries');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_delivery_items');
    }
};
