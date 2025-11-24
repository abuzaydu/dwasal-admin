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
        Schema::create('pick_drop_requisitions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('vehicle_route_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->string('start_point');
            $table->string('end_point');
            $table->string('request_type');
            $table->string('type');
            $table->date('effective_date')->nullable();
            $table->string('status')->default('Pending');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vehicle_route_id')->references('id')->on('vehicle_routes');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pick_drop_requisitions');
    }
};
