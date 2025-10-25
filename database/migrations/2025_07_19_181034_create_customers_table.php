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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('client_id')->index()->nullable();
            $table->unsignedBigInteger('customer_category_id')->nullable();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('tin')->nullable();
            $table->boolean('check_last_sale')->default(false);
            $table->integer('cust_no');
            $table->string('vrn')->nullable();
            $table->string('country_code')->nullable()->default('TZ');
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();
            $table->string('street')->nullable();
            $table->integer('cust_id_type')->default(6); //For VFD
            $table->string('custid')->nullable(); //For VFD
            $table->datetime('time_created');
            $table->boolean('is_active')->default(true);
            $table->integer('default_due_days')->default(30);
            $table->decimal('due_amount_limit', 15, 2)->default(0);
            $table->string('payment_reference')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
