<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //

    public function serviceSaleItem()
    {
        return $this->hasMany(ServiceSaleItem::class);
    }

    public function shops()
    {
        return $this->belongsto(Shop::class);
    }

    public function categories()
    {
        return $this->belongsToMany(ServCategory::class);
    }
}
