<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refuel extends Model
{
    protected $guarded = [];

    public function fuelType(){
        return $this->belongsTo(FuelType::class);
    }
    public function driver(){
        return $this->belongsTo(Driver::class);
    }
     public function fuelStation()
    {
        return $this->belongsTo(FuelStation::class);
    }
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
