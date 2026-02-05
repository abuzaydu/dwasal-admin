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
        Schema::table('part_usages', function (Blueprint $table) {
            $table->boolean('is_approved')->default(false);
            $table->string('approved_by')->nullable();
            $table->datetime('approved_at')->nullable();
            $table->string('reject_reason')->nullable();
            $table->string('closed_by')->nullable();
            $table->datetime('closed_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('part_usages', function (Blueprint $table) {
            $table->dropColumn('is_approved');
            $table->dropColumn('approved_by');
            $table->dropColumn('approved_at');
            $table->dropColumn('reject_reason');
            $table->dropColumn('closed_by');
            $table->dropColumn('closed_at');
        });
    }
};
