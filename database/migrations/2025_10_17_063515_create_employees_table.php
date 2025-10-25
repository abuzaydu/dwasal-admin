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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('position_id')->nullable();
            $table->string('emp_id')->index();
            $table->string('type');
            $table->string('fname');
            $table->string('mname')->nullable();
            $table->string('lname');
            $table->string('nin')->nullable();
            $table->string('tin')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('gender');
            $table->string('marital_status');
            $table->boolean('have_md_condition')->default(false);
            $table->text('address')->nullable();
            $table->string('mobile');
            $table->string('email');
            $table->boolean('is_paid_monthly')->default(true);
            $table->decimal('basic_pay_hourly', 15,2)->nullable()->default(0);
            $table->decimal('basic_pay_monthly', 15,2)->nullable()->default(0);
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('ssf')->default('NSSF');
            $table->string('mif')->default('NHIF');
            $table->boolean('is_reg_ssf')->default(true);
            $table->boolean('is_reg_mif')->default(true);
            $table->boolean('is_reg_wcf')->default(true);
            $table->boolean('allow_deduct_heslb')->default(false);
            $table->decimal('trans_allowance', 15,2)->nullable()->default(0);
            $table->decimal('house_allowance', 15,2)->nullable()->default(0);
            $table->decimal('com_allowance', 15,2)->nullable()->default(0);
            $table->foreign('position_id')->references('id')->on('positions');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_deleted')->default(false);
            $table->string('deleted_by')->nullable();
            $table->boolean('has_user_account')->default(false);
            $table->boolean('has_co_special_loan')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
