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
        Schema::create('pp_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('production_stage_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->boolean('is_wip_stage')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('production_stage_id')->references('id')->on('production_stages');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pp_stages');
    }
};
