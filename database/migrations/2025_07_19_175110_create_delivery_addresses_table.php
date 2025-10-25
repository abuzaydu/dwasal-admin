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
        Schema::create('delivery_addresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('longitude')->nullable();
            $table->string('latitude')->nullable();
            $table->string('plus_code')->nullable();
            $table->string('address1')->nullable();
            $table->string('postcode')->nullable();
            $table->string('locality')->nullable();
            $table->string('state')->nullable();
            $table->string('country');
            $table->string('distance')->nullable();
            $table->string('estimated_travel_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
