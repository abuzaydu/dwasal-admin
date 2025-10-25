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
        Schema::create('exp_utilized_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('exp_utilization_id')->index();
            $table->date('date');
            $table->string('expense_type');
            $table->decimal('ut_qty',15, 2);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('exp_utilization_id')->references('id')->on('exp_utilizations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_utilized_items');
    }
};
