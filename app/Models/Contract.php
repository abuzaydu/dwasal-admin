<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $guarded = [];

        public function shop()
        {
            return $this->belongsTo(Shop::class);
        }
    
        public function serviceCharge()
        {
            return $this->belongsTo(ServiceCharge::class);
        }
}
