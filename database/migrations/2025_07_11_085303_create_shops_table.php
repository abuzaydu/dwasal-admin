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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('business_type_id')->index();
            $table->unsignedBigInteger('subscription_type_id')->index();
            $table->string('suid')->unique();
            $table->string('name');
            $table->string('tel')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('email')->nullable();
            $table->string('street')->nullable();
            $table->string('district')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('logo_location')->nullable();
            $table->string('tin')->nullable();
            $table->string('vrn')->nullable();
            $table->string('postal_address')->nullable();
            $table->string('physical_address')->nullable();
            $table->string('short_desc')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_hq')->default(false);
            $table->boolean('is_warehouse')->default(false);
            $table->unsignedBigInteger('parent_shop_id')->nullable();
            $table->boolean('is_default_warehouse')->default(false);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('subscription_type_id')->references('id')->on('subscription_types');
            $table->foreign('business_type_id')->references('id')->on('business_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
