<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to avoid doctrine/dbal dependency for column type changes.
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'fcm_token')) {
            DB::statement('ALTER TABLE users MODIFY fcm_token TEXT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'fcm_token')) {
            DB::statement('ALTER TABLE users MODIFY fcm_token VARCHAR(255) NULL');
        }
    }
};
