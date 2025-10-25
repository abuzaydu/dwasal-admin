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
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('washing_equipment_id')->index();
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->string('maintenance_type');
            $table->text('description_of_wo');
            $table->string('technician');
            $table->string('inspection_findings')->nullable();
            $table->string('parts_used')->nullable();
            $table->decimal('cost', 20,4)->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('washing_equipment_id')->references('id')->on('washing_equipment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_records');
    }
};
