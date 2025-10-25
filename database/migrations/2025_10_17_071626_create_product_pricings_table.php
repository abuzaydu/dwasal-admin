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
        Schema::create('product_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('product_id')->index();
            $table->date('date');
            $table->string('defcurr')->default('TZS');
            $table->string('currency')->default('EUR');
            $table->decimal('ex_rate', 15,2)->default(2600);
            $table->integer('min_order_value')->default(5000);
            $table->integer('no_of_piece_per_set')->default(1);
            $table->decimal('shipping_import_fee')->default(30);
            $table->decimal('wholesale_eu_margin')->default(100);
            $table->decimal('vat')->default(20);
            $table->decimal('target_rrp', 15, 2)->default(0);
            $table->decimal('domestic_w_margin')->default(30);
            $table->decimal('domestic_r_margin')->default(50);
            $table->boolean('is_pending')->default(true);
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_pricings');
    }
};
