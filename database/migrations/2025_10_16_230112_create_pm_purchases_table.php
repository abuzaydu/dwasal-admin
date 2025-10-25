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
        Schema::create('pm_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('supplier_id')->index();
            $table->string('grn_no')->nullable();
            $table->string('order_no')->nullable();
            $table->string('delivery_note_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('purchase_type')->nullable()->default('cash');;
            $table->decimal('total_amount', 15, 2)->nullable()->default(0);
            $table->decimal('amount_paid', 15, 2)->nullable()->default(0);
            $table->text('comments')->nullable();
            $table->string('status')->nullable();
            $table->datetime('date');
            $table->string('currency')->nullable();
            $table->string('defcurr')->nullable();
            $table->decimal('ex_rate', 15, 5)->default(1);
            $table->boolean('is_deleted')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_purchases');
    }
};
