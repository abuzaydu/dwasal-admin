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
        Schema::create('expense_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('expense_item_id')->index();
            $table->string('expense_type');
            $table->decimal('unit_cost', 15, 2)->nullable()->default(0);
            $table->decimal('qty', 15,2)->default(1);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('has_vat')->default('no')->nullable();
            $table->decimal('wht_rate', 5, 2)->default(0);
            $table->text('description')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_temps');
    }
};
