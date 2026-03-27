<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleRequisition extends Model
{
    protected $guarded =  [];

     protected $casts = [
        'requisition_date' => 'date',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
        'cancelled_at'     => 'datetime',
    ];
 
 
    public function approvedBy()        { return $this->belongsTo(User::class, 'approved_by'); }
    public function rejectedBy()        { return $this->belongsTo(User::class, 'rejected_by'); }
 
    public function requisitionTripLog()
    {
        return $this->hasOne(RequisitionTripLog::class, 'vehicle_requisition_id');
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class);
    }

    public function purpose()
    {
        return $this->belongsTo(RequisitionPurpose::class, 'requisition_purpose_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle()
    { 
    return $this->belongsTo(Vehicle::class); 
    }

    public function tripLogs()
    {
        return $this->hasMany(RequisitionTripLog::class, 'vehicle_requisition_id');
    }

}
