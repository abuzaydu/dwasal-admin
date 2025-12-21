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
        Schema::create('vms_expense_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vms_expense_id')->index();
            $table->unsignedBigInteger('expense_type_id')->index();
            $table->decimal('quantity', 15,2)->default(1);
            $table->decimal('unit_price', 15,2)->nullable()->default(0);
            $table->decimal('total_price', 15,2)->nullable()->default(0);
            $table->foreign('vms_expense_id')->references('id')->on('vms_expenses');
            $table->foreign('expense_type_id')->references('id')->on('expense_types');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vms_expense_items');
    }
};
