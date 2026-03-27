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
        Schema::table('vms_expenses', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['trip_type_id']);

            $table->unsignedBigInteger('vehicle_id')->nullable()->change();
            $table->unsignedBigInteger('employee_id')->nullable()->change();
            $table->unsignedBigInteger('vendor_id')->nullable()->change();
            $table->unsignedBigInteger('trip_type_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vms_expenses', function (Blueprint $table) {
            
            $table->dropForeign('vms_expenses_employee_id_foreign');
            $table->dropForeign('vms_expenses_trip_type_id_foreign');

            $table->unsignedBigInteger('employee_id')->nullable(false)->change();
            $table->unsignedBigInteger('trip_type_id')->nullable(false)->change();

            $table->unsignedBigInteger('vehicle_id')->nullable(false)->change();
            $table->unsignedBigInteger('vendor_id')->nullable(false)->change();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('trip_type_id')->references('id')->on('trip_types');
        });
    }
};
