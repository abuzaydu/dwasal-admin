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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('maintenance_type_id')->index();
            $table->date('date');
            $table->string('maintenance_code');
            $table->string('req_type');
            $table->string('priority');
            $table->string('service_title');
            $table->string('charge_bear_by')->nullable();
            $table->decimal('charge', 15,2)->nullable()->default(0);
            $table->text('remarks')->nullable();
            $table->string('status')->default('Pending');
            $table->boolean('is_deleted')->default(false);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('maintenance_type_id')->references('id')->on('maintenance_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
