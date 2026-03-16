<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmsExpense extends Model
{
    protected $guarded = [];

    public function tripType() {
        return $this->belongsTo(TripType::class);
    }

    public function items() {
        return $this->hasMany(VmsExpenseItem::class);
    }

    public function vehicle() {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
