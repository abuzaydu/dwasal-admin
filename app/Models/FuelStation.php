<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelStation extends Model
{
    protected $guarded = [];

    public function vendor(){
        return $this->belongsTo(Vendor::class);
    }
    public function refuels(){
        return $this->hasMany(Refuel::class);
    }
}
