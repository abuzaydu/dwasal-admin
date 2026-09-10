<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Restore the Face ID schema when its original migration was removed. */
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'face_embedding')) {
                $table->json('face_embedding')->nullable();
            }
            if (!Schema::hasColumn('employees', 'face_model_version')) {
                $table->string('face_model_version')->nullable();
            }
            if (!Schema::hasColumn('employees', 'face_registered_at')) {
                $table->timestamp('face_registered_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            foreach (['face_embedding', 'face_model_version', 'face_registered_at'] as $column) {
                if (Schema::hasColumn('employees', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
