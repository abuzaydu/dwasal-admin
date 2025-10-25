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
        Schema::create('cash_ins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('account_id')->index();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->unsignedBigInteger('trans_id')->nullable();
            $table->unsignedBigInteger('rm_trans_id')->nullable();
            $table->unsignedBigInteger('pm_trans_id')->nullable();
            $table->unsignedBigInteger('cash_out_id')->nullable();
            $table->string('category')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('source');
            $table->datetime('in_date');
            $table->boolean('is_loan')->default(false);
            $table->decimal('amount_paid', 15,2)->nullable()->default(0);
            $table->string('status')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_ins');
    }
};
