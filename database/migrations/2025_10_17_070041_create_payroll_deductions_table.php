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
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('m_payroll_id')->index();
            $table->date('date');
            $table->string('name');
            $table->decimal('amount', 15,2);
            $table->boolean('is_added_to_ledger')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('m_payroll_id')->references('id')->on('m_payrolls');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
