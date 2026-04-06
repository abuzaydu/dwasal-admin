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
            $table->unsignedBigInteger('shop_id')->index()->after('company_id');
            $table->string('contract_type')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('no_of_children')->nullable()->default(0);
            $table->string('ssf_no')->nullable();
        });

        Schema::table('payroll_temps', function (Blueprint $table) {
            $table->renameColumn('penalty', 'recovery');
            $table->text('note')->nullable();
        });

        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('penalty', 'recovery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('shop_id');
            $table->dropColumn('contract_type');
            $table->dropColumn('birth_date');
            $table->dropColumn('no_of_children');
            $table->dropColumn('ssf_no');
        });

        Schema::table('payroll_temps', function (Blueprint $table) {
            $table->renameColumn('recovery', 'penalty');
            $table->dropColumn('note');
        }); 
    
        Schema::table('payrolls', function (Blueprint $table) {
            $table->renameColumn('recovery', 'penalty');
        }); 
    }
};
