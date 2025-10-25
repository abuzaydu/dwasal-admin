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
        Schema::create('rm_purchase_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('raw_material_id')->index();
            $table->unsignedBigInteger('rm_purchase_temp_id')->index();
            $table->decimal('qty', 8,2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade');
            $table->foreign('rm_purchase_temp_id')->references('id')->on('rm_purchase_temps');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_purchase_item_temps');
    }
};
