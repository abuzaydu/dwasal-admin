<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LicenseType extends Model
{
    protected $guarded = [];

    public function company(){
        return $this->belongsTo(Company::class);
    }
     public function drivers()
    {
        return $this->hasMany(Driver::class);
    }
}

