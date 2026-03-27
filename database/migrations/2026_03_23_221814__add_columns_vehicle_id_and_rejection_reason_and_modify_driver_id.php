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
        Schema::table('vehicle_requisitions', function (Blueprint $table) {
            $table->unsignedBigInteger('driver_id')->nullable()->change();
            $table->unsignedBigInteger('vehicle_id')->nullable()->after('driver_id');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table ->string('rejection_reason')->nullable()->after('status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicle_requisitions', function (Blueprint $table) {
            $table->dropForeign(['vehicle_id']);
            $table -> dropColumn('vehicle_id');
            $table -> dropColumn('rejection_reason');


        });
    }
};
