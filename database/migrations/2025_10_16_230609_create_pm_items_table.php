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
        Schema::create('pm_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('pm_purchase_id')->nullable();
            $table->unsignedBigInteger('packing_material_id')->index();
            $table->decimal('qty', 8,2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->datetime('date');
            $table->decimal('qty_out', 15,2)->default(0);
            $table->boolean('is_utilized')->default(false);
            $table->boolean('is_deleted')->default(false); 
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('pm_purchase_id')->references('id')->on('pm_purchases')->onDelete('cascade');
            $table->foreign('packing_material_id')->references('id')->on('packing_materials')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_items');
    }
};
