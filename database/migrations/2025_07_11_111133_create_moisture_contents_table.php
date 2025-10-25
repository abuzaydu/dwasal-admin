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
        Schema::create('moisture_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('quality_test_id')->index();
            $table->decimal('moisture_percentage', 8, 2);
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('quality_test_id')->references('id')->on('quality_tests');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moisture_contents');
    }
};
