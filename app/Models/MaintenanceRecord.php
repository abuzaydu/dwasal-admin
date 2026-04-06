<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceRecord extends Model
{
    protected $table = 'maintenance_records';

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }

    public function washingEquipment()
    {
        return $this->belongsTo(WashingEquipment::class, 'washing_equipment_id');
    }

    public function photos()
    {
        return $this->hasMany(MaintenancePhoto::class, 'maintenance_record_id')
            ->where('photo_url', 'like', 'maintenance/equipment%');
    }
}
