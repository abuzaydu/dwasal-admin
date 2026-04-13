<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ownerships', function (Blueprint $table) {
            $table->string('slug', 64)->nullable()->after('type');
            $table->boolean('is_system')->default(false)->after('description');
            $table->unsignedTinyInteger('sort_order')->default(0)->after('is_system');
        });

        Schema::table('ownerships', function (Blueprint $table) {
            $table->unique(['company_id', 'slug']);
        });

        foreach (\App\Models\Company::query()->pluck('id') as $companyId) {
            \App\Models\Ownership::syncDefaultsForCompany((int) $companyId);
        }
    }

    public function down(): void
    {
        Schema::table('ownerships', function (Blueprint $table) {
            $table->dropUnique(['company_id', 'slug']);
        });

        Schema::table('ownerships', function (Blueprint $table) {
            $table->dropColumn(['slug', 'is_system', 'sort_order']);
        });
    }
};
