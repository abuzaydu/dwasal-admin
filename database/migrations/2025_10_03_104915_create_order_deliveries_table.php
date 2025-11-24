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
        Schema::create('order_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('order_detail_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index()->nullable();
            $table->unsignedBigInteger('delivery_address_id')->index();
            $table->string('status')->default('Loading');
            $table->text('remarks')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            // $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('delivery_address_id')->references('id')->on('delivery_addresses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_deliveries');
    }
};
