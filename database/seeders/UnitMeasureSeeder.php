<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UnitMeasure;

class UnitMeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $units = array(
            'm³',
            'yd³',
            't',
            'pc',
            'box',
            'set',
            'bundle',
            'roll',
            'pair',
            'ctn',
            'bag',
            'pkg',
            'kg',
            'doz',
            'caps',
            'tabs',
            'tube',
            'ft',
            'ltr',
            'mtr',
            'crate',
            'gal',
            'bottle',
            'tray',
            'bucket',
            'bunch',
            'can',
            'cylinder',
            'container',
            'rim',
            'tin',
            'sqm',
            'strip',
            'blister',
            'cbm',
            'unit'
        );

        foreach ($units as $key => $value) {
            $um = UnitMeasure::where('unit_name', $value)->first();
            if (is_null($um)) {
                $um = new UnitMeasure();
                $um->unit_name = $value;
                $um->save();
            }
        }
    
    }
}
