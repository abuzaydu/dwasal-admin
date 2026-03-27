<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $guarded = [];

    public function refuels()
    {
        return $this->hasMany(Refuel::class);
    }

    public function licenseType()
    {
        return $this->belongsTo(LicenseType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currentVehicle()
    {
        return $this->belongsTo(Vehicle::class, 'current_vehicle_id');
    }

    public function documents()
    {
        // Emergency/mobile access: show legal documents for the driver's current vehicle.
        return $this->hasMany(LegalDocument::class, 'vehicle_id', 'current_vehicle_id');
    }
}
