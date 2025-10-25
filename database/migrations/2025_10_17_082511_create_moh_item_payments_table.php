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
        Schema::create('moh_item_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('moh_cost_id')->index();
            $table->unsignedBigInteger('moh_cost_payment_id')->index();
            $table->datetime('pay_date');
            $table->decimal('paid_amt', 15,2);
            $table->foreign('moh_cost_id')->references('id')->on('moh_costs');
            $table->foreign('moh_cost_payment_id')->references('id')->on('moh_cost_payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moh_item_payments');
    }
};
