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
        Schema::create('rm_sourcings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('raw_material_source_id')->index();
            $table->unsignedBigInteger('storage_location_id')->index();
            $table->date('sourcing_date');
            $table->decimal('qty_received', 20,4);
            $table->string('unit_of_measure');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('raw_material_source_id')->references('id')->on('raw_material_sources');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rm_sourcings');
    }
};
