<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Session;
use App\Models\Shop;

class ProductExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $shop = Shop::find(Session::get('shop_id'));
        return $shop->products()->get();
    }
}
