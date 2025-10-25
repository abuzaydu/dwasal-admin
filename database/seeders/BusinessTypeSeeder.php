<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\BusinessType;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $btypes = array(
            ["type" => "Manufacturing Business","description" => "Millings, Bread Bakery, Funiture, Candle Making, Jewelry Manufacturer, Herbal Remedy Production, Spice Manufacturer, Cosmetic Manufacturer, Ceramic Tiles Manufacturer","type_sw" => "Biashara ya Uzalishaji","description_sw" => "","type_icon" => ""],
            ["type" => "Merchandising Business","description" => "Supermarkets, Mini Shops, Clothing, Shoes, Building Materials, Accessories, Electrical Equipment, Spare Parts, Agricultural and Livestock Inputs, Pharmacies, Food and Beverages etc","type_sw" => "Biashara ya Uuzaji","description_sw" => "","type_icon" => ""],
            ["type" => "Service Business","description" => "Transport Services, Hotels, Real Estate, Insuarance Services, Schools, Consultancy Services, Graphic Design, Car Wash, Parking Services, Repair, Priniting,Salon, etc","type_sw" => "Biashara ya Huduma","description_sw" => "","type_icon" => ""],
            ["type" => "Merchandising & Service Business","description" => "Stationary, Dispensary, Garages, Restaurants, etc.","type_sw" => "Zote 2 & 3","description_sw" => "","type_icon" => ""]
        );

        foreach ($btypes as $key => $bt) {
            BusinessType::create($bt);
        }
    }
}
