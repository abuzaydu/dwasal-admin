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
        Schema::create('purchase_cost_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_id')->index();
            $table->string('item_desc')->nullable();
            $table->decimal('percent')->nullable();
            $table->decimal('amount', 15,2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_cost_items');
    }
};
