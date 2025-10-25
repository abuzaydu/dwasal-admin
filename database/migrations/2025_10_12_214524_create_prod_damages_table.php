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
        Schema::create('prod_damages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('stock_id')->nullable();
            $table->decimal('quantity');
            $table->text('reason')->nullable();
            $table->decimal('deph_measure', 10,2)->nullable()->default(0);
            $table->decimal('buying_price')->nullable();
            $table->decimal('selling_price');
            $table->decimal('in_stock', 10, 2)->nullable()->default(0);
            $table->datetime('time_created');
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
        Schema::dropIfExists('prod_damages');
    }
};
