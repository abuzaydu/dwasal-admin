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
        Schema::create('payment_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_detail_id')->index();
            $table->string('reference')->nullable();
            $table->string('msisdn');
            $table->decimal('amount', 20,4);
            $table->string('status')->default('PENDING');
            $table->datetime('creation_date')->nullable();
            $table->string('transid')->nullable();
            $table->string('channel')->nullable();
            $table->string('utilityref')->nullable();
            $table->string('resultcode')->nullable();
            $table->string('payment_token')->nullable();
            $table->text('payment_gateway_url')->nullable();
            $table->boolean('is_real')->default(false);
            $table->foreign('order_detail_id')->references('id')->on('order_details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_details');
    }
};
