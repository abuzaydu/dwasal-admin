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
            $table->json('fingerprint_template')->nullable()->after('face_registered_at');
            $table->string('fingerprint_model_version')->nullable()->after('fingerprint_template');
            $table->string('fingerprint_algorithm_version')->nullable()->after('fingerprint_model_version');
            $table->timestamp('fingerprint_registered_at')->nullable()->after('fingerprint_algorithm_version');
            $table->timestamp('fingerprint_last_verified_at')->nullable()->after('fingerprint_registered_at');
            $table->string('fingerprint_finger')->nullable()->after('fingerprint_last_verified_at');
            $table->boolean('fingerprint_enabled')->default(true)->after('fingerprint_finger');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'face_embedding', 
                'face_model_version', 
                'face_registered_at',
                'fingerprint_template',
                'fingerprint_model_version',
                'fingerprint_algorithm_version',
                'fingerprint_registered_at',
                'fingerprint_last_verified_at',
                'fingerprint_finger',
                'fingerprint_enabled',
            ]);
        });
    }
};
