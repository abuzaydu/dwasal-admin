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
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->unsignedBigInteger('purchase_order_id')->index()->nullable();
            $table->integer('order_no')->nullable();
            $table->integer('grn_no');
            $table->string('delivery_note_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->decimal('total_amount', 15, 2)->nullable()->default(0);
            $table->decimal('amount_paid', 15, 2)->nullable()->default(0);
            $table->decimal('total_cost', 15,2)->nullable()->default(0);
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->string('purchase_type')->nullable()->default('cash');
            $table->string('status')->default('Pending');
            $table->text('comments')->nullable();
            $table->datetime('time_created');
            $table->boolean('is_production')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
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
        Schema::dropIfExists('purchases');
    }
};
