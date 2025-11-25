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
        Schema::create('delivery_note_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_note_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->decimal('delivery_qty', 15,2);
            $table->string('uom');
            $table->timestamps();
        });

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('delivery_address_id')->nullable();
            $table->string('status')->default('Loading');
           
        });

        Schema::table('delivery_addresses', function (Blueprint $table) {
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_note_items');

        Schema::table('delivery_notes', function (Blueprint $table) {
            $table->dropColumn('vehicle_id');
            $table->dropColumn('delivery_address_id');
            $table->dropColumn('status');
        });

        Schema::table('delivery_addresses', function (Blueprint $table) {
            $table->dropColumn('customer_id');
        });
    }
};
