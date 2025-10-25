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
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('purchase_id')->nullable()->index();
            $table->unsignedBigInteger('storage_location_id')->index()->nullable();
            $table->unsignedBigInteger('production_run_id')->index()->nullable();
            $table->decimal('quantity_in', 20,4);
            $table->decimal('quantity_out', 20,4)->nullable()->default(0);
            $table->boolean('is_utilized')->default(false);
            $table->decimal('unit_cost')->nullable()->default(0);//Supplier Price
            $table->decimal('supp_unit_cost', 20,4)->nullable()->default(0);//Supplier Price + Additional costs
            $table->string('source');
            $table->bigInteger('transfer_order_id')->nullable();
            $table->datetime('stock_date');
            $table->date('expire_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            // $table->foreign('purchase_id')->references('id')->on('purchases');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('product_id')->references('id')->on('products');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stocks');
    }
};
