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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->unsignedBigInteger('license_type_id')->index();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('license_no');
            $table->date('license_issue_date');
            $table->string('nid');
            $table->date('join_date');
            $table->string('working_time_slot');
            $table->date('date_of_birth');
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('driver_photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
