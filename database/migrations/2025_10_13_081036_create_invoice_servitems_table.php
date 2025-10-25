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
        Schema::create('invoice_servitems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pro_invoice_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->float('repeatition');
            $table->decimal('cost_per_unit', 15, 2);
            $table->decimal('amount', 15, 2);
            $table->decimal('disc_percent', 4, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('total_discount', 15, 2)->default(0);
            $table->string('with_vat')->default('no')->nullable();
            $table->decimal("tax_amount", 15,2)->nullable()->default(0);
            $table->datetime('time_created');
            $table->foreign('pro_invoice_id')->references('id')->on('pro_invoices')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_servitems');
    }
};
