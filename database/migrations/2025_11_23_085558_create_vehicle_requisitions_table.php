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
        Schema::create('vehicle_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('vehicle_type_id')->index();
            $table->unsignedBigInteger('requisition_purpose_id')->index();
            $table->unsignedBigInteger('driver_id')->index();
            $table->string('from');
            $table->string('to');
            $table->string('pick_up');
            $table->date('requisition_date')->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->string('tolerance_duration')->nullable();
            $table->string('no_of_passenger')->nullable();
            $table->text('details');
            $table->string('status')->default('Awaiting for Approval');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('vehicle_type_id')->references('id')->on('vehicle_types');
            $table->foreign('requisition_purpose_id')->references('id')->on('requisition_purposes');
            $table->foreign('driver_id')->references('id')->on('drivers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_requisitions');
    }
};
