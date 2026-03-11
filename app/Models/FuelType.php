<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FuelType extends Model
{
    protected $guarded = [];

    public function refuels(){
        return $this->HasMany(Refuel::class);
    }
}
