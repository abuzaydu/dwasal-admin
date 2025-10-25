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
        Schema::create('invoice_approvals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('pro_invoice_id')->index();
            $table->string('approver')->nullable();
            $table->string('status')->default('Awaiting for Approval');
            $table->text('comments')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->foreign('pro_invoice_id')->references('id')->on('pro_invoices');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_approvals');
    }
};
