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
        Schema::create('washing_plants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('plant_name')->unique();
            $table->string('plant_location');
            $table->decimal('capacity_per_day', 20,4);
            $table->string('unit_of_measure');
            $table->string('operating_hours')->nullable();
            $table->date('launch_date')->nullable();
            $table->date('last_maintenance_date')->nullable();
            $table->string('photo_url')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('washing_plants');
    }
};
