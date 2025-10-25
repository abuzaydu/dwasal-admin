<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionType;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stypes = array(
            ['title' => 'Enterprise', 'description' => ''],
        );

        foreach ($stypes as $key => $st) {
            SubscriptionType::create($st);
        }
    }
}
