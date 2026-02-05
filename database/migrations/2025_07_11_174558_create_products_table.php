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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('sand_type_id')->index()->nullable();
            $table->unsignedBigInteger('storage_location_id')->index()->nullable();
            $table->string('product_code')->nullable();
            $table->string('barcode')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('basic_uom');
            $table->text('short_desc');
            $table->text('description')->nullable();
            $table->decimal('in_stock', 20,4)->nullable()->default(0);
            $table->decimal('unit_cost', 20,4)->nullable();
            $table->decimal('retail_price', 20,4)->nullable();
            $table->decimal('wholesale_price', 20,4)->nullable();
            $table->boolean('is_by_product')->default(false);
            $table->string('image_url')->nullable();
            $table->string('status')->nullable()->default('In Stock');
            $table->integer('reorder_point')->default(1);
            $table->string('location')->nullable();
            $table->string('type')->nullable()->index();
            $table->string('model')->nullable()->index();
            $table->string('brand')->nullable()->index();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('length')->nullable();
            $table->string('width')->nullable();
            $table->string('height')->nullable();
            $table->string('thick')->nullable();
            $table->string('volume')->nullable();
            $table->string('weight')->nullable();
            $table->datetime('time_created');
            $table->boolean('is_active')->default(true);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};