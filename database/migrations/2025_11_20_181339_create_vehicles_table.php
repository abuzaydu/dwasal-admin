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
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('vehicle_type_id')->index();
            $table->unsignedBigInteger('ownership_id')->index();
            $table->unsignedBigInteger('department_id')->index()->nullable();
            $table->string('plate_no');
            $table->string('vehicle_name');
            $table->string('chassis_no')->nullable();
            $table->date('reg_date')->nullable();
            $table->string('model')->nullable();
            $table->string('capacity')->nullable();
            $table->string('uom')->nullable();
            $table->string('status')->default('Available');
            $table->boolean('is_assigned')->default(false);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
            $table->foreign('ownership_id')->references('id')->on('ownerships');
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
