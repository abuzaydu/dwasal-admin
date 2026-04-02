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
        Schema::table('employees', function (Blueprint $table) {
            $table->json('face_embedding')->nullable()->after('has_co_special_loan');
            $table->string('face_model_version')->nullable()->after('face_embedding');
            $table->timestamp('face_registered_at')->nullable()->after('face_model_version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['face_embedding', 'face_model_version', 'face_registered_at']);
        });
    }
};
