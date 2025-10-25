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
        Schema::create('depreciations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('asset_record_id')->index();
            $table->string('year');
            $table->decimal('value_begin_yr', 15,2)->nullable()->default(0);
            $table->decimal('dep_amount', 15,2)->nullable()->default(0);
            $table->decimal('value_end_yr', 15,2)->nullable()->default(0);
            $table->foreign('asset_record_id')->references('id')->on('asset_records');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depreciations');
    }
};
