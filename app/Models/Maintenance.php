<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $table = 'maintenances';
    protected $guarded = [];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function maintenanceType()
    {
        return $this->belongsTo(MaintenanceType::class, 'maintenance_type_id');
    }

    public function items()
    {
        return $this->hasMany(MaintenanceItem::class, 'maintenance_id')
            ->where('is_deleted', false);
    }

    /**
     * vehicle-maintenance photos are stored in `maintenance_photos`
     * using `maintenance_record_id = maintenances.id`.
     */
    public function photos()
    {
        return $this->hasMany(MaintenancePhoto::class, 'maintenance_record_id')
            ->where('photo_url', 'like', 'maintenance/vehicle%');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
