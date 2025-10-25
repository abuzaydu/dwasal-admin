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
        Schema::create('rm_purchase_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->string('date_set')->default('auto');
            $table->date('date')->nullable();
            $table->string('purchase_type')->nullable();
            $table->string('grn_no')->nullable();
            $table->string('order_no')->nullable();
            $table->string('delivery_note_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('pay_type')->default('Cash');
            $table->string('currency')->nullable();
            $table->string('defcurr')->nullable();
            $table->string('ex_rate_mode')->default('Locale');
            $table->decimal('local_ex_rate', 15, 5)->default(1);
            $table->decimal('foreign_ex_rate', 15, 5)->default(1);
            $table->decimal('ex_rate', 15, 5)->default(1);
            $table->date('due_date')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('rm_purchase_temps');
    }
};
