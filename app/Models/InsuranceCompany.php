<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceCompany extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'description',
        'active',
    ];

    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'insurance_company_id');
    }
}
