<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IrPeriod extends Model
{
    protected $fillable = [
        'company_id',
        'period',
        'description',
        'active',
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'ir_period_id');
    }
}
