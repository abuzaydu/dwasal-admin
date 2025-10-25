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
        Schema::create('discount_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('sale_temp_id')->index();
            $table->unsignedBigInteger('an_sale_id')->index()->nullable();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('approver')->nullable();
            $table->decimal('disc_percent', 8,2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->string('status')->default('Awaiting for Approval');
            $table->string('comments')->nullable();
            $table->datetime('approved_time')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_approvals');
    }
};
