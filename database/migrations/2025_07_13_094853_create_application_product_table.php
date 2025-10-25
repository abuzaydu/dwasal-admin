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
        Schema::create('application_product', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('application_id')->index();
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('application_id')->references('id')->on('applications');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_product');
    }
};
