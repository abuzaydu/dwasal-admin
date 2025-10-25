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
        Schema::create('transfer_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->unsignedBigInteger('requester_id')->index()->nullable();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('destination_id')->index();
            $table->unsignedBigInteger('an_sale_id')->nullable();
            $table->unsignedBigInteger('source_product_id')->nullable();
            $table->integer('order_no');
            $table->date('order_date');
            $table->text('reason')->nullable();
            $table->boolean('add_vat')->default(true);
            $table->decimal('source_product_quantity' ,15, 2)->nullable();
            $table->boolean('is_transfomation_transfer')->default(false);
            $table->string('status')->default('Pending');
            $table->boolean('is_mix_transfer')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_request')->default(false);
            $table->boolean('is_return')->default(false);
            $table->datetime('confirm_time')->nullable();
            $table->datetime('receive_time')->nullable();
            $table->boolean('is_transfer_to_rm')->default(false);
            $table->string('received_by')->nullable();
            $table->string('on_confirm_remarks')->nullable();
            $table->string('on_receive_remarks')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('destination_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('source_product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_orders');
    }
};
