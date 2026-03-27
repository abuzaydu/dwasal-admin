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
        Schema::create('requisition_trip_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_requisition_id')->index();
            $table->string('trip_no')->unique();
            $table->date('start_time')->nullable();
            $table->date('end_time')->nullable();
            $table->decimal('start_odometer', 10, 2)->nullable();
            $table->decimal('end_odometer', 10, 2)->nullable();
            $table->string('status')->default('Pending'); 
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->foreign('vehicle_requisition_id')->references('id')->on('vehicle_requisitions')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_trip_logs');
    }
};
