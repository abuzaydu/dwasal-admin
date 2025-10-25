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
        Schema::create('pm_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pm_transfer_id')->index();
            $table->unsignedBigInteger('packing_material_id')->index();
            $table->integer('qty');
            $table->integer('src_qty');
            $table->integer('des_qty')->nullable()->default();
            $table->decimal('unit_cost', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pm_transfer_items');
    }
};
