<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionRun extends Model
{
    public function washingPlant()
    {
        return $this->belongsTo(WashingPlant::class);
    }
}
