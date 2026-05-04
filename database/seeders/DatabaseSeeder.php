<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //$this->call(BusinessTypeSeeder::class);
        //$this->call(SubscriptionSeeder::class);
        $this->call(RoleAndPermissionSeeder::class);
       // $this->call(UserSeeder::class);
        $this->call(UnitMeasureSeeder::class);
        $this->call(COASeeder::class);
    }
}
