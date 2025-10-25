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
        Schema::create('an_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('sale_order_id')->index()->nullable();
            $table->unsignedBigInteger('pro_invoice_id')->index()->nullable();
            $table->unsignedBigInteger('bank_detail_id')->nullable();
            $table->integer('invoice_no');
            $table->string('lpo_no')->nullable();
            $table->decimal('sale_amount', 15, 2)->nullable()->default(0);
            $table->decimal('sale_discount', 15, 2)->nullable()->default(0);
            $table->decimal('tax_amount', 15, 2)->nullable()->default(0);
            $table->decimal('return_amount', 15,2)->nullable()->default(0);
            $table->decimal('return_discount', 15, 2)->nullable()->default(0);
            $table->decimal('return_tax', 15, 2)->nullable()->default(0);
            $table->decimal('sale_amount_paid', 15, 2)->nullable()->default(0);
            $table->string('sale_type')->nullable();
            $table->string('pay_type')->default('Cash');
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->datetime('time_created');
            $table->date('rent_end_date')->nullable();
            $table->string('status')->default('Unpaid');
            $table->boolean('is_paid')->default(false);
            $table->datetime('time_paid')->nullable();
            $table->date('due_date');
            $table->boolean('is_pass_due')->default(false);
            $table->integer('no_nots')->nullable()->default(0);
            $table->string('vehicle_no')->nullable();
            $table->string('comments')->nullable();
            $table->text('note')->nullable();
            $table->boolean('is_stock_requested')->default(false);
            $table->string('is_property_invoice')->default('No');
            $table->string('ref_customer')->nullable();
            $table->integer('sync_id')->nullable();
            $table->bigInteger('grade_id')->nullable();
            $table->string('year')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->string('del_by')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('bank_detail_id')->references('id')->on('accounts');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('an_sales');
    }
};
