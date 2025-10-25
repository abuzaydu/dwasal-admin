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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->integer('supp_id')->nullable();
            $table->string('name');
            $table->string('contact_no')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->string('country_code')->nullable()->default('TZ');
            $table->string('supplier_for');
            $table->string('bank_name')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->datetime('time_created');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
