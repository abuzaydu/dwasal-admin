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
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index()->nullable();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->index()->nullable();
            $table->unsignedBigInteger('address_id')->index();
            $table->unsignedBigInteger('delivery_address_id')->index();
            $table->unsignedBigInteger('payment_mode_id')->index()->nullable();
            $table->unsignedBigInteger('payment_detail_id')->index()->nullable();
            $table->string('uuid');
            $table->decimal('total', 20,4)->nullable()->default(0);
            $table->decimal('delivery_cost', 20, 4)->nullable()->default(0);
            $table->decimal('tax_amount', 20, 4)->nullable()->default(0);
            $table->integer('duration')->nullable();
            $table->datetime('exp_delivery_time')->nullable();
            $table->string('status')->default('Pending');
            $table->string('processed_by')->nullable();
            $table->boolean('is_invoice_created')->default(false);
            $table->boolean('is_full_delivered')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('address_id')->references('id')->on('addresses');
            $table->foreign('delivery_address_id')->references('id')->on('delivery_addresses');
            $table->foreign('payment_mode_id')->references('id')->on('payment_modes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
