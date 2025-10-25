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
        Schema::create('serv_category_service', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('serv_category_id');
            $table->unsignedBigInteger('service_id');
            $table->foreign('serv_category_id')->references('id')->on('serv_categories');
            $table->foreign('service_id')->references('id')->on('services');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serv_category_service');
    }
};
