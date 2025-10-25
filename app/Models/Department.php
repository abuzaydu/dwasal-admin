<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    //
   protected $guarded = ['id'];


   public function  employees(){

      return $this->belongsToMany(Employee::class,'department_employee','department_id','employee_id',);
   }
}
