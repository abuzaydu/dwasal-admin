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
        Schema::create('leave_rosters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('approver_id')->nullable();
            $table->string('type');
            $table->string('reason')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('attachment')->nullable();
            $table->string('status')->nullable();
            $table->string('approved_by')->nullable();
            $table->text('approve_comments')->nullable();
            $table->boolean('is_rejected')->default(false);
            $table->text('reject_reason')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('approver_id')->references('id')->on('employees')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_rosters');
    }
};
