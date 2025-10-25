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
        Schema::create('carousels', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('sub_title');
            $table->text('details')->nullable();
            $table->string('img_url')->nullable();
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carousels');
    }
};
