<?php

namespace App\Models;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $guarded = ['id'];

    //one position has many employees
    public function employee()
    {
        return $this->hasMany(Employee::class);
    }
}
