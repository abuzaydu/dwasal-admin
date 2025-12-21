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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('part_category_id')->index();
            $table->unsignedBigInteger('part_location_id')->index();
            $table->string('part_no');
            $table->string('part_name');
            $table->string('uom');
            $table->decimal('av_qty')->nullable()->default(0);
            $table->text('description')->nullable();
            $table->string('remarks')->nullable();
            $table->string('status')->default('Active');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('part_category_id')->references('id')->on('part_categories');
            $table->foreign('part_location_id')->references('id')->on('part_locations');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
