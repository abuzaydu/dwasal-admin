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
        Schema::create('vms_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('trip_type_id')->index();
            $table->string('exp_group');//this one should be removed later on
            $table->string('trip_no');
            $table->decimal('odometer_mileage', 15,2);
            $table->decimal('vehicle_rent', 15,2);
            $table->date('date');
            $table->text('remarks')->nullable();
            $table->string('status')->default('Pending');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('trip_type_id')->references('id')->on('trip_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vms_expenses');
    }
};
