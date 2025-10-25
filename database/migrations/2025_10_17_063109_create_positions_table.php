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
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->string('name');
            $table->decimal('basic_pay_hourly', 15,2)->nullable()->default(0);
            $table->decimal('basic_pay_monthly', 15,2)->nullable()->default(0);
            $table->decimal('trans_allowance', 15,2)->nullable()->default(0);
            $table->decimal('house_allowance', 15,2)->nullable()->default(0);
            $table->decimal('com_allowance', 15,2)->nullable()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
