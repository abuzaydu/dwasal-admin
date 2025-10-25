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
        Schema::create('rm_use_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('rm_use_id')->index();
            $table->unsignedBigInteger('raw_material_id')->index();
            $table->decimal('quantity', 15, 2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->datetime('date');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('rm_use_id')->references('id')->on('rm_uses')->onDelete('cascade');
            $table->foreign('raw_material_id')->references('id')->on('raw_materials')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_use_items');
    }
};
