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
        Schema::create('invoice_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('product_unit_id')->index();
            $table->decimal('quantity', 15, 2);
            $table->decimal('cost_per_unit', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->decimal('disc_percent', 4, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->string('sold_in')->default('Retail Price');
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("vat_amount", 15, 2)->nullable()->default(0);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_item_temps');
    }
};
