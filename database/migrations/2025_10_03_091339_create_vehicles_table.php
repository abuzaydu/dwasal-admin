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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('plate_no');
            $table->string('chassis_no')->nullable();
            $table->string('type');
            $table->string('model')->nullable();
            $table->string('capacity')->nullable();
            $table->string('uom')->nullable();
            $table->string('status')->default('Available');
            $table->string('ownership')->default('Company');
            $table->boolean('is_assigned')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
