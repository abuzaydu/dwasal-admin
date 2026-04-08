<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Remove legacy placeholder ownerships only if nothing references them anymore.
        // This is intentionally conservative to avoid breaking existing vehicles.
        DB::table('ownerships')
            ->whereNull('slug')
            ->whereIn('type', ['COMPANY', 'OTHERS'])
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('vehicles')
                    ->whereColumn('vehicles.ownership_id', 'ownerships.id');
            })
            ->delete();
    }

    public function down(): void
    {
        // Irreversible data cleanup.
    }
};

