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
        Schema::create('dlc_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('direct_labour_cost_id')->index();
            $table->unsignedBigInteger('production_stage_id')->index();
            $table->decimal('qty', 8,2);
            $table->decimal('unit_cost', 15, 2);
            $table->decimal('total', 15, 2);
            $table->datetime('date');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('direct_labour_cost_id')->references('id')->on('direct_labour_costs')->onDelete('cascade');
            $table->foreign('production_stage_id')->references('id')->on('production_stages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dlc_items');
    }
};
