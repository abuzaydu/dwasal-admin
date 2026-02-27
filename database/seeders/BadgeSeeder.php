<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $companyId = 2;

    for ($i = 1; $i <= 10; $i++) {

        $number = str_pad($i, 3, '0', STR_PAD_LEFT);

        \App\Models\Badge::firstOrCreate(
            [
                'company_id' => $companyId,
                'badge_number' => $number
            ],
            [
                'status' => 'available'
            ]
        );
    }
}

}
