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
        Schema::create('attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->time('start_of_day');
            $table->time('end_of_day');
            $table->string('max_overtime')->nullable();
            $table->boolean('works_on_weekend')->default(false);
            $table->time('w_start_of_day')->nullable();
            $table->time('w_end_of_day')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_settings');
    }
};
