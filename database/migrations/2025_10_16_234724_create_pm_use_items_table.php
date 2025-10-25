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
        Schema::create('pm_use_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('packing_material_id')->index();
            $table->unsignedBigInteger('product_packed')->nullable();
            $table->unsignedBigInteger('pm_use_id')->index();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->datetime('date');
            $table->decimal('unit_packed', 15, 2); 
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('packing_material_id')->references('id')->on('packing_materials')->onDelete('cascade');
            $table->foreign('pm_use_id')->references('id')->on('pm_uses')->onDelete('cascade');
            $table->foreign('product_packed')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_use_items');
    }
};
