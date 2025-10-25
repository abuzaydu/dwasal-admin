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
        Schema::create('sale_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_temp_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_unit_id')->index();
            $table->decimal('quantity_sold', 15, 2);
            $table->decimal('curr_stock', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('buying_price', 15, 2)->default(0);
            $table->decimal('retail_price', 15, 2);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('disc_percent', 4, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->string('used_stock')->nullable();
            $table->string('sold_in')->default('Retail Price');
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("vat_amount", 15,2)->nullable()->default(0);
            $table->foreign('sale_temp_id')->references('id')->on('sale_temps');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('product_unit_id')->references('id')->on('product_units')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_item_temps');
    }
};
