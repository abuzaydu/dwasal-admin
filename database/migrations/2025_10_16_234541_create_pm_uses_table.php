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
        Schema::create('pm_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('production_cost_id')->index();
            $table->decimal('total_cost', 15, 2);
            $table->bigInteger('prod_batch');
            $table->datetime('date');
            $table->unsignedBigInteger('pm_purchase_id')->nullable();
            $table->text('comments')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('production_cost_id')->references('id')->on('production_costs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_uses');
    }
};
