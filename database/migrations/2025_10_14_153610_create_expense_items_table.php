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
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('expense_category_id')->index();
            $table->string('expense_type');
            $table->boolean('is_cost_of_sale')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
