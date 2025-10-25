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
        Schema::create('washing_equipment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('washing_plant_id')->index();
            $table->string('equipment_code')->unique();
            $table->string('equipment_name');
            $table->string('equipment_type');
            $table->string('manufacturer');
            $table->string('model');
            $table->date('installation_date');
            $table->string('maintenance_schedule');
            $table->date('last_maintenance_date')->nullable()->default(null);
            $table->date('next_maintenance_date')->nullable()->default(null);
            $table->string('status')->default('Active');
            $table->string('photo_url')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('washing_plant_id')->references('id')->on('washing_plants');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('washing_equipment');
    }
};
