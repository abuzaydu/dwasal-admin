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
        Schema::create('transfer_order_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('destination_id')->nullable()->index();
            $table->unsignedBigInteger('an_sale_id')->nullable()->index();
            $table->integer('transfer_type')->default(0);
            $table->date('order_date')->nullable();
            $table->text('reason')->nullable();
            $table->boolean('is_mix_transfer')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_order_temps');
    }
};
