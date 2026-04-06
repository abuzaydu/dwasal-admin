<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequisitionTripLog extends Model
{
    protected $guarded = [];

    public function vehicleRequisition()
    {
        return $this->belongsTo(VehicleRequisition::class);
    }

    public function expenses()
    {
        return $this->hasMany(VmsExpense::class, 'requisition_trip_log_id');
    }
    
}
