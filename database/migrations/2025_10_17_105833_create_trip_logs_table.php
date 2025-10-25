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
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('an_sale_id')->index()->nullable();
            $table->unsignedBigInteger('device_id')->index();
            $table->datetime('trip_date');
            $table->datetime('trip_end_date')->nullable();
            $table->string('trip_title');
            $table->decimal('trip_price', 15,2)->nullable();
            $table->string('from');
            $table->string('to')->nullable();
            $table->decimal('mileage_out', 15,2);
            $table->decimal('mileage_in', 15, 2)->nullable();
            $table->decimal('fuel_start', 15,2)->nullable();
            $table->decimal('fuel_end', 15,2)->nullable();
            $table->decimal('fuel', 15,2)->nullable();
            $table->decimal('fuel_unit_cost', 15, 2)->nullable();
            $table->string('driver')->nullable();
            $table->string('container_no')->nullable();
            $table->string('container_size')->nullable();
            $table->string('bill_no')->nullable();
            $table->string('shipping')->nullable();
            $table->string('gross_weight')->nullable();
            $table->string('net_weight')->nullable();
            $table->string('load_type')->nullable();
            $table->boolean('is_transit')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->foreign('an_sale_id')->references('id')->on('an_sales');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trip_logs');
    }
};
