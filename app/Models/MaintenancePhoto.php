<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenancePhoto extends Model
{
    protected $table = 'maintenance_photos';

    public function vehicleMaintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_record_id');
    }
}
