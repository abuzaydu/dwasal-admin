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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->boolean('allow_multi_currency')->default(false);
            $table->boolean('allow_more_product_desc')->default(false);
            $table->decimal('tax_rate')->default(18);
            $table->string('inv_no_type')->nullable();
            $table->boolean('is_vat_registered')->default(false);
            $table->boolean('set_vat_by_default')->default(false);
            $table->boolean('estimate_withholding_tax')->default(false);
            $table->boolean('use_barcode')->default(false);
            $table->boolean('always_sell_old')->default(true);
            $table->boolean('allow_sp_less_bp')->default(false);
            $table->boolean('is_service_per_device')->default(false);
            $table->boolean('is_rental_service')->default(false);
            $table->string('currency_words')->default('Tanzania')->nullable();
            $table->boolean('retail_with_wholesale')->default(false);
            $table->boolean('discount_by_percent')->default(true);
            $table->boolean('allow_unit_discount')->default(false);
            $table->boolean('is_school')->default(false);
            $table->boolean('enable_exp_date')->default(false);
            $table->boolean('is_filling_station')->default(false);
            $table->boolean('show_bd')->default(false);
            $table->boolean('is_agent')->default(false);
            $table->boolean('enable_cpos')->default(false);
            $table->boolean('change_price_for_all_store')->default(false);
            $table->integer('sp_mindays')->default(3);
            $table->boolean('enable_exp_pay_approval')->default(false);
            $table->boolean('is_categorized')->default(false);
            $table->boolean('show_discounts')->default(true);
            $table->boolean('show_end_note')->default(true);
            $table->boolean('enable_auto_note')->default(true);
            $table->string('invoice_title_position')->nullable()->default('right');
            $table->string('invoice_title')->default('TAX INVOICE');
            $table->string('invoice_temp')->nullable();
            $table->string('invoice_color')->default('#DB8700');
            $table->string('invoice_title_color')->default('black');
            $table->boolean('show_declaration')->default(true);
            $table->boolean('show_authorization_sign')->default(true);
            $table->boolean('enable_efd')->default(false);
            $table->boolean('generate_barcode')->default(false);
            $table->boolean('use_vfd_only')->default(false);
            $table->boolean('always_issue_vfd')->default(false);
            $table->boolean('use_production_module')->default(false);
            $table->boolean('enable_packaging')->default(false);
            $table->boolean('enable_sale_approval')->default(false);
            $table->boolean('sale_with_low_stock')->default(false);
            $table->boolean('allow_initiate_sto_from_invoice')->default(false);
            $table->boolean('enable_hr_payroll_module')->default(false);
            $table->boolean('always_print_invoice')->default(true);
            $table->boolean('show_qty_in_stmt')->default(false); 
            $table->datetime('dc_time')->nullable();
            $table->integer('rvl_days')->default(30);
            $table->integer('exp_rl_days')->default(30);
            $table->string('invoice_end_note')->nullable()->default('This is an electronic Invoice and is valid without the signature and seal');
            $table->boolean('enable_trip_logs')->default(false);
            $table->boolean('is_hotel')->default(false);
            $table->boolean('is_livestock')->default(false);
            $table->boolean('is_manuf_with_merch')->default(false);
            $table->boolean('is_manufacturing_with_service')->default(false);
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
