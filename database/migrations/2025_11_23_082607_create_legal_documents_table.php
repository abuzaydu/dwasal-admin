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
        Schema::create('legal_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('vehicle_id')->index();
            $table->unsignedBigInteger('document_type_id')->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->date('last_issue_date');        
            $table->date('expire_date');
            $table->decimal('charge_paid', 15,2);
            $table->decimal('commission', 15,2);
            $table->string('doc_attachment');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('document_type_id')->references('id')->on('document_types');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_documents');
    }
};
