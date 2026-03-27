<?php

namespace App\Models;

use App\Models\ExpenseAttachment;
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
    public function driver() {
        return $this->belongsTo(Driver::class);
    }

    public function employee() {
        return $this->belongsTo(Employee::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function attachments()
    {
        return $this->hasMany(VmsExpenseAttachment::class, 'vms_expense_id');
    }

    public function tripLog()
    {
        return $this->belongsTo(RequisitionTripLog::class, 'requisition_trip_log_id');
    }
}
