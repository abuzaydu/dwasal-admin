<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = [];

    public function refuels(){
        return $this->hasMany(Refuel::class);
    }
    public function licenseType()
{
    return $this->belongsTo(LicenseType::class);
}
}
