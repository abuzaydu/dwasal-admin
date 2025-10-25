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
        Schema::create('plc_item_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('prod_labour_cost_id')->index();
            $table->unsignedBigInteger('plc_payment_id')->index();
            $table->datetime('pay_date');
            $table->decimal('paid_amt', 15,2);
            $table->foreign('prod_labour_cost_id')->references('id')->on('prod_labour_costs');
            $table->foreign('plc_payment_id')->references('id')->on('plc_payments');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plc_item_payments');
    }
};
