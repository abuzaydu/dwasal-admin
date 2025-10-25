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
        Schema::create('m_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('user_id')->index();
            $table->date('month')->index();
            $table->decimal('basic_salaries', 15,2)->nullable()->default(0);
            $table->decimal('trans_allowance', 15,2)->nullable()->default(0);
            $table->decimal('house_allowance', 15,2)->nullable()->default(0);
            $table->decimal('com_allowance', 15,2)->nullable()->default(0);
            $table->decimal('overtime', 15, 2)->nullable()->default(0);
            $table->decimal('bonuses', 15, 2)->nullable()->default(0);
            $table->decimal('paye', 15, 2)->nullable()->default(0);
            $table->decimal('ssf', 15,2)->nullable()->default(0);
            $table->decimal('mif', 15,2)->nullable()->default(0);
            $table->decimal('wcf', 15,2)->nullable()->default(0);
            $table->decimal('heslb', 15,2)->nullable()->default(0);
            $table->decimal('emp_loan', 15,2)->nullable()->default(0);
            $table->decimal('absences', 15, 2)->nullable()->default(0);
            $table->decimal('lates', 15, 2)->nullable()->default(0);
            $table->decimal('other_deductions', 15, 2)->nullable()->default(0);
            $table->boolean('is_added_to_expense')->default(false);
            $table->boolean('is_deleted')->default(false);
            $table->string('deleted_by')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_payrolls');
    }
};
