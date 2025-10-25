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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->string('code')->nullable();
            $table->string('name')->unique();
            $table->decimal('price', 15, 2)->nullable()->default(0);
            $table->text('description')->nullable();
            $table->boolean('active_for_sale')->default(true);
            $table->datetime('time_created');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
