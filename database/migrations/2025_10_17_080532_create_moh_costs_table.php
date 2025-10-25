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
        Schema::create('moh_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('production_cost_id')->index();
            $table->integer('moh_no');
            $table->datetime('date');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_paid', 15, 2)->nullable()->default(0);
            $table->text('remarks')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->foreign('production_cost_id')->references('id')->on('production_costs');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moh_costs');
    }
};
