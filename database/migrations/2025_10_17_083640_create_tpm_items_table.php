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
        Schema::create('tpm_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->index();
            $table->unsignedBigInteger('source_product_id')->index();
            $table->unsignedBigInteger('destin_product_id')->index();
            $table->unsignedBigInteger('source_pm_id')->index()->nullable();
            $table->unsignedBigInteger('destin_pm_id')->index()->nullable();
            $table->integer('source_pm_qty')->nullable()->default(0);
            $table->integer('destin_pm_qty')->nullable()->default(0);
            $table->foreign('order_id')->references('id')->on('transfer_orders');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpm_items');
    }
};
