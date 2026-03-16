<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Vendor extends Model
{
    protected $guarded = [];


    public function fuelStation()
    {
        return $this->hasMany(FuelStation::class);
    }

    public function expenseType()
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
