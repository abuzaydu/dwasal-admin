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
        Schema::create('an_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('an_sale_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_unit_id')->index();
            $table->unsignedBigInteger('stock_id')->index()->nullable();
            $table->unsignedBigInteger('category_id')->index()->nullable();
            $table->decimal('quantity_sold', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('retail_price', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('disc_percent', 4, 2)->default(0);
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('total_discount', 15, 2)->nullable()->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->string('sold_in')->default('Retail Price');
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->decimal('input_tax', 15, 2)->nullable()->default(0);
            $table->integer('sync_id')->nullable();
            $table->datetime('time_created');
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('product_unit_id')->references('id')->on('product_units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('an_sale_items');
    }
};
