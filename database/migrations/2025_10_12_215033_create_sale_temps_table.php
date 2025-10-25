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
        Schema::create('sale_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->boolean('is_from_so')->default(false);
            $table->unsignedBigInteger('sale_order_id')->nullable()->index();
            $table->unsignedBigInteger('pro_invoice_id')->nullable()->index();
            $table->string('date_set')->default('auto');
            $table->date('sale_date')->nullable();
            $table->string('sale_type')->nullable();
            $table->string('sold_in')->default('Retail Price');
            $table->string('pay_type')->default('Cash');
            $table->string('currency')->nullable();
            $table->string('defcurr')->nullable();
            $table->string('ex_rate_mode')->default('Foreign');
            $table->decimal('local_ex_rate', 15, 5)->default(1);
            $table->decimal('foreign_ex_rate', 15, 5)->default(1);
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->date('due_date')->nullable();
            $table->decimal('sale_discount', 15, 2)->nullable()->default(0);
            $table->text('comments')->nullable();
            $table->string('is_property_invoice')->default('No');
            $table->string('ref_customer')->nullable();
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
        Schema::dropIfExists('sale_temps');
    }
};
