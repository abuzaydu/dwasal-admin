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
        Schema::create('packing_material_shop', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('packing_material_id')->index();
            $table->unsignedBigInteger('pm_for')->nullable();
            $table->decimal('in_store', 15, 2)->nullable()->default(0);
            $table->decimal('unit_cost', 15, 2)->nullable()->default(0);
            $table->integer('reorder_point')->nullable()->default(1);
            $table->text('description')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('packing_material_id')->references('id')->on('packing_materials')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packing_material_shop');
    }
};
