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
            $table->unsignedBigInteger('requisition_trip_log_id')->nullable()->after('status');
            $table->foreign('requisition_trip_log_id')->references('id')->on('requisition_trip_logs')->OnDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vms_expenses', function (Blueprint $table) {
             $table->dropForeign(['requisition_trip_log_id']);            
             $table->dropColumn('requisition_trip_log_id');

        });
    }
};
