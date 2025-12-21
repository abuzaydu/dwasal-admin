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
        Schema::create('part_usage_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_usage_id')->index();
            $table->unsignedBigInteger('part_category_id')->index();
            $table->unsignedBigInteger('part_id')->index();
            $table->decimal('pu_qty', 15, 2)->default(1);
            $table->datetime('date');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('part_usage_id')->references('id')->on('part_usages');
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
        Schema::dropIfExists('part_usage_items');
    }
};
