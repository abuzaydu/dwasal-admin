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
        Schema::create('refuels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('fuel_type_id')->index();
            $table->unsignedBigInteger('fuel_station_id')->index();
            $table->unsignedBigInteger('driver_id')->index();
            $table->decimal('odometer', 15,2);
            $table->decimal('fuel_qty', 15,2);
            $table->decimal('price', 15,2);
            $table->decimal('total_cost', 15,2);
            $table->date('date');
            $table->time('time');
            $table->string('note')->nullable();
            $table->string('doc_attachment');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('fuel_type_id')->references('id')->on('fuel_types');
            $table->foreign('fuel_station_id')->references('id')->on('fuel_stations');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refuels');
    }
};
