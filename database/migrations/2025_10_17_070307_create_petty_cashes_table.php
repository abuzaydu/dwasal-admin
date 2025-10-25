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
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('account_id')->nullable();
            $table->datetime('request_date');
            $table->datetime('received_date')->nullable();
            $table->string('ref_no')->nullable();
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('approver')->nullable();
            $table->text('reject_reason')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->string('status')->default('Awaiting for Approval');
            $table->string('issued_by')->nullable();
            $table->datetime('issued_date')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('pay_mode');
            $table->boolean('is_from_hq')->default(false);
            $table->unsignedBigInteger('hq_shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('petty_cashes');
    }
};
