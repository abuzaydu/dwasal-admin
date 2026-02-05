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
        Schema::table('shops', function (Blueprint $table) {
            $table->string('stamp')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('use_invoice_banner')->default(false);
            $table->string('banner_url')->nullable();
            $table->string('stamp')->nullable();
        });

        Schema::table('devices', function (Blueprint $table) {
            $table->string('device_name')->nullable()->change();
        });

        Schema::table('part_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shops', function (Blueprint $table) {
            $table->dropColumn('stamp');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('use_invoice_banner');
            $table->dropColumn('banner_url');
            $table->dropColumn('stamp');
        });
    }
};
