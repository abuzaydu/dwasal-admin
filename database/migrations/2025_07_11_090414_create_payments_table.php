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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->string('req_uid')->unique();
            $table->string('reference')->nullable();
            $table->string('phone_number');
            $table->decimal('amount_paid', 15, 2);
            $table->string('code');
            $table->string('period')->default('Monthly');
            $table->datetime('activation_time')->nullable();
            $table->string('status')->nullable();
            $table->datetime('creation_date')->nullable();
            $table->string('transid')->nullable();
            $table->string('channel')->nullable();
            $table->string('utilityref')->nullable();
            $table->string('resultcode')->nullable();
            $table->string('payment_token')->nullable();
            $table->text('payment_gateway_url')->nullable();
            $table->datetime('expire_date')->nullable();
            $table->boolean('is_expired')->default(true);
            $table->boolean('is_for_module')->default(false);
            $table->integer('module')->nullable()->default(0);
            $table->integer('subscr_type')->default(1);
            $table->boolean('is_real')->default(false);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
