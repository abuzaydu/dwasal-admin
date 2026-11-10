<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_operations_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('record_no');
            $table->date('record_date');
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('contact')->nullable();
            $table->string('location')->nullable();
            $table->time('sign_in_time')->nullable();
            $table->time('sign_out_time')->nullable();
            $table->decimal('fuel_in', 12, 2)->nullable();
            $table->decimal('fuel_out', 12, 2)->nullable();
            $table->unsignedInteger('quantity_of_trips')->nullable();
            $table->text('machine_condition')->nullable();
            $table->text('signature')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'record_no']);
        });

        Schema::create('field_operation_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_operations_record_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('row_number');
            $table->string('truck_no')->nullable();
            $table->decimal('amount_per_trip', 14, 2)->nullable();
            $table->unsignedInteger('trip_count')->nullable();
            $table->decimal('cubic', 12, 2)->nullable();
            $table->json('expenditure_entries')->nullable();
            $table->json('cash_entries')->nullable();
            $table->timestamps();

            $table->unique(['field_operations_record_id', 'row_number'], 'field_op_rows_record_row_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_operation_rows');
        Schema::dropIfExists('field_operations_records');
    }
};