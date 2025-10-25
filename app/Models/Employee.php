<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];

    public function  department(){

        return $this->belongsToMany(Department::class, 'department_employee', 'employee_id', 'department_id');
    }
}
