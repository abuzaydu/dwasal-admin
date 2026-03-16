<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripType extends Model
{
    protected $guarded = [];

    public function expenses() {
        return $this->hasMany(VmsExpense::class);
    }
}
