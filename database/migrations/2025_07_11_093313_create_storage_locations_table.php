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
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('location_name');
            $table->decimal('longitude', 11,8);
            $table->decimal('latitude', 10,8);
            $table->decimal('capacity', 20,4)->nullable();
            $table->string('unit')->nullable();
            $table->string('storage_for');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('storage_locations');
    }
};
