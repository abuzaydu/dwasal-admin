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
        Schema::create('device_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('device_id')->index();
            $table->unsignedBigInteger('expense_id')->index();
            $table->foreign('device_id')->references('id')->on('devices');
            $table->foreign('expense_id')->references('id')->on('expenses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('device_expenses');
    }
};
