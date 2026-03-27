<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    protected $table = 'maintenance_types';
    protected $guarded = ['id'];

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'maintenance_type_id');
    }
}
