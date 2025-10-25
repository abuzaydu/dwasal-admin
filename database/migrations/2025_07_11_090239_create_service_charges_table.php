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
        Schema::create('service_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_type_id')->index();
            $table->decimal('initial_pay', 15, 2);
            $table->decimal('next_pay', 15, 2)->nullable();
            $table->string('duration');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_charges');
    }
};
