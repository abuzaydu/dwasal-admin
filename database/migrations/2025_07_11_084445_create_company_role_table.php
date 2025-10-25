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
        Schema::create('company_role', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('role_id')->index();
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('role_id')->references('id')->on('roles');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_role');
    }
};
