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
        Schema::create('part_purchase_temps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('vendor_id')->index()->nullable();
            $table->datetime('pp_date');
            $table->string('currency');
            $table->string('defcurr');
            $table->decimal('ex_rate', 15, 9)->default(1);
            $table->string('purchase_type')->nullable()->default('cash');
            $table->string('pay_type')->default('Cash');
            $table->string('status')->default('Pending');
            $table->string('comments')->nullable();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('part_purchase_temps');
    }
};
