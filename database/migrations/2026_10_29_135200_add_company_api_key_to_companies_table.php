<?php

use App\Models\Company;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('companies', 'company_api_key')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('company_api_key', 64)->nullable()->unique()->after('cuid');
            });
        }

        Company::query()->orderBy('id')->each(function (Company $company) {
            if (empty($company->company_api_key)) {
                $company->forceFill([
                    'company_api_key' => Company::generateUniqueApiKey(),
                ])->save();
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('companies', 'company_api_key')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropUnique(['company_api_key']);
                $table->dropColumn('company_api_key');
            });
        }
    }
};
