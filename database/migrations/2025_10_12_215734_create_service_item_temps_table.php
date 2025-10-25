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
        Schema::create('service_item_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sale_temp_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->float('no_of_repeatition')->default(1);
            $table->decimal('price', 15, 2)->default(0);
            $table->decimal('disc_percent', 4, 2)->default(0);
            $table->decimal('discount', 15, 2)->difault(0);
            $table->decimal('total_discount', 15,2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("vat_amount", 15, 2)->nullable()->default(0);
            $table->foreign('sale_temp_id')->references('id')->on('sale_temps');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_item_temps');
    }
};
