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
        Schema::create('delivery_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('pro_invoice_id')->nullable();
            $table->unsignedBigInteger('an_sale_id')->nullable();
            $table->integer('note_no');
            $table->text('comments');
            $table->string('issued_by')->nullable();
            $table->string('received_by')->nullable();
            $table->foreign('an_sale_id')->references('id')->on('an_sales')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_notes');
    }
};
