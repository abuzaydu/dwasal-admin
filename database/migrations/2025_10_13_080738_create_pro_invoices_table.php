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
        Schema::create('pro_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('bank_detail_id')->nullable();
            $table->boolean('is_quotation')->default(false);
            $table->integer('quote_no')->nullable();
            $table->integer('invoice_no')->nullable();
            $table->decimal('net_amount',15,2)->nullable()->default(0);
            $table->string('ref_no')->nullable();
            $table->text('summary')->nullable();
            $table->date('due_date');
            $table->string('status')->default('Awaiting for Approval');
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 6)->default(1);            
            $table->decimal('discount', 15, 2)->nullable()->default(0);
            $table->decimal('shipping_cost', 15, 2)->nullable()->default(0);
            $table->decimal('adjustment', 15, 2)->nullable()->default(0);
            $table->text('notes')->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->datetime('time_created');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pro_invoices');
    }
};
