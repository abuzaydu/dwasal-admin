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
        Schema::create('plc_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('prod_labour_cost_id')->index();
            $table->unsignedBigInteger('production_stage_id')->index();
            $table->decimal('quantity', 15, 2)->default(1);
            $table->decimal('unit_cost', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('prod_labour_cost_id')->references('id')->on('prod_labour_costs')->onDelete('cascade');
            $table->foreign('production_stage_id')->references('id')->on('production_stages')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plc_items');
    }
};
