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
            if (!Schema::hasColumn('employees', 'fingerprint_template')) {
                $table->json('fingerprint_template')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_model_version')) {
                $table->string('fingerprint_model_version')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_algorithm_version')) {
                $table->string('fingerprint_algorithm_version')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_registered_at')) {
                $table->timestamp('fingerprint_registered_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_last_verified_at')) {
                $table->timestamp('fingerprint_last_verified_at')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_finger')) {
                $table->string('fingerprint_finger')->nullable();
            }
            if (!Schema::hasColumn('employees', 'fingerprint_enabled')) {
                $table->boolean('fingerprint_enabled')->default(true);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $columns = [
                'fingerprint_template', 'fingerprint_model_version',
                'fingerprint_algorithm_version', 'fingerprint_registered_at',
                'fingerprint_last_verified_at', 'fingerprint_finger',
                'fingerprint_enabled',
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
