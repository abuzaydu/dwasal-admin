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
        Schema::create('labour_costs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_pricing_id')->index();
            $table->foreign('product_pricing_id')->references('id')->on('product_pricings');
            $table->string('stage');
            $table->decimal('daily_wage_rate', 15,2)->default(0);
            $table->integer('no_of_piece')->default(1);
            $table->decimal('cost_per_piece')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('labour_costs');
    }
};
