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
        Schema::create('part_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_purchase_id')->index();
            $table->unsignedBigInteger('part_category_id')->index();
            $table->unsignedBigInteger('part_id')->index();
            $table->datetime('date');
            $table->decimal('pp_qty', 15, 2)->default(1);
            $table->decimal('unit_price', 15, 2)->nullable()->default(0);
            $table->decimal('total_price', 15, 2)->nullable()->default(0);
            $table->boolean('is_deleted')->default(false);
            $table->foreign('part_purchase_id')->references('id')->on('part_purchases');
            $table->foreign('part_category_id')->references('id')->on('part_categories');
            $table->foreign('part_id')->references('id')->on('parts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_purchase_items');
    }
};
