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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('quote_request_id')->nullable();
            $table->string('quote_number', 50)->unique();
            $table->string('customer_name', 125)->nullable();
            $table->string('email', 125);
            $table->string('phone', 125);
            $table->string('address', 125)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status', 50)->default('Draft'); // Draft, Sent, Accepted, Rejected, Expired
            $table->boolean('is_proinvoice_created')->default(false);
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->string('created_by', 125)->nullable();
            $table->timestamps();

            $table->foreign('quote_request_id')->references('id')->on('quote_requests')->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
