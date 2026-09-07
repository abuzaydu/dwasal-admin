<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Department;
use App\Models\Position;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
        'face_embedding' => 'array',
        'face_registered_at' => 'datetime',
        'fingerprint_template' => 'array',
        'fingerprint_registered_at' => 'datetime',
        'fingerprint_last_verified_at' => 'datetime',
        'fingerprint_enabled' => 'boolean',
    ];

    public function  department(){

        return $this->belongsToMany(Department::class, 'department_employee', 'employee_id', 'department_id');
    }
    
    public function  company(){
        return $this->belongsTo(Company::class);
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }
      
}
