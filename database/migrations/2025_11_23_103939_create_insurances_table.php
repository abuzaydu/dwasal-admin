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
        Schema::create('insurances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('insurance_company_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('ir_period_id')->index();
            $table->string('policy_number');
            $table->decimal('charge_payable', 15,2);
            $table->decimal('deductible', 15,2);
            $table->date('start_date');        
            $table->date('end_date');
            $table->date('recurring_date')->nullable();
            $table->string('policy_attachment');
            $table->boolean('add_reminder')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('description')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('insurance_company_id')->references('id')->on('insurance_companies');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('ir_period_id')->references('id')->on('ir_periods');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insurances');
    }
};
