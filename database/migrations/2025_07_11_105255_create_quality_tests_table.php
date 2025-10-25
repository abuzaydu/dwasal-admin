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
        Schema::create('quality_tests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('production_run_id')->index();
            $table->datetime('test_date');
            $table->string('test_type');
            $table->string('result');
            $table->boolean('passed')->default(true);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('production_run_id')->references('id')->on('production_runs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quality_tests');
    }
};
