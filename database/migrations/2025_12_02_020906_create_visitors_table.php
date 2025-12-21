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
        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('host_id')->index();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('name');
            $table->string('mobile');
            $table->string('email')->nullable();
            $table->string('address');
            $table->string('id_type');
            $table->string('id_number')->nullable();
            $table->string('visitor_photo')->nullable();
            $table->string('badge_no')->nullable();
            $table->string('purpose');
            $table->string('status')->default('Awaiting Host permission');
            $table->boolean('is_granted')->default(false);
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('host_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitors');
    }
};
