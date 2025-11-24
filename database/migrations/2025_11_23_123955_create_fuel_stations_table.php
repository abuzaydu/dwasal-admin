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
        Schema::create('fuel_stations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->string('station_name');        
            $table->string('contact_person')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->boolean('active')->default(true);
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fuel_stations');
    }
};
