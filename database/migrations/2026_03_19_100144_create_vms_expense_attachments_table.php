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
        Schema::create('vms_expense_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vms_expense_id');
            $table->string('file_path');
            $table->string('file_type')->nullable();

            $table->foreign('vms_expense_id')->references('id')->on('vms_expenses')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vms_expense_attachments');
    }
};
