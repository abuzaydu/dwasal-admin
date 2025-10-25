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
        Schema::create('purchase_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_temp_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('quantity_in', 15,2)->default(0);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('retail_price', 15,2)->default(0);
            $table->date('expire_date')->nullable();
            $table->decimal('total', 15, 2)->default(0);
            $table->foreign('purchase_temp_id')->references('id')->on('purchase_temps')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_item_temps');
    }
};
