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
        Schema::create('advance_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('employee_id')->index();
            $table->date('date');
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_paid', 15,2)->nullable()->default(0);
            $table->string('status')->default('Awaiting for Approval');
            $table->string('remarks')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advance_salaries');
    }
};
