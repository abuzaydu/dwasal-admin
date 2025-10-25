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
        Schema::create('asset_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('dep_method');
            $table->string('asset_name');
            $table->string('asset_class');
            $table->string('description');
            $table->string('physical_location');
            $table->string('asset_number');
            $table->string('serial_no')->nullable();
            $table->date('acquisition_date');
            $table->decimal('acquisition_cost', 15,2)->default(0);
            $table->float('useful_life');
            $table->decimal('salvage_value', 15, 2);
            $table->decimal('first_year', 8,2)->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_records');
    }
};
