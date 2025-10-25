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
        Schema::create('sender_ids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sms_account_id')->index();
            $table->string('name');
            $table->boolean('auto_sms')->default(false);
            $table->foreign('sms_account_id')->references('id')->on('sms_accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sender_ids');
    }
};
