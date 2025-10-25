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
        Schema::create('production_runs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('washing_plant_id')->index();
            // $table->unsignedBigInteger('raw_material_source_id')->index()->nullable();
            $table->unsignedBigInteger('storage_location_id')->index();
            $table->string('pr_no');
            $table->datetime('start_time');
            $table->datetime('end_time')->nullable();
            $table->decimal('input_quantity', 20,4);
            $table->decimal('output_quantity', 20,4)->nullable();
            $table->decimal('waste_water_quantity', 20,4)->nullable();
            $table->string('status')->default('In Progress');
            $table->text('remarks')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('washing_plant_id')->references('id')->on('washing_plants');
            // $table->foreign('raw_material_source_id')->references('id')->on('raw_material_sources');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_runs');
    }
};
