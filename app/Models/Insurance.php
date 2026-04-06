<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Insurance extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'insurance_company_id',
        'vehicle_id',
        'ir_period_id',
        'policy_number',
        'charge_payable',
        'deductible',
        'start_date',
        'end_date',
        'recurring_date',
        'policy_attachment',
        'add_reminder',
        'is_active',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'recurring_date' => 'date',
        'add_reminder' => 'boolean',
        'is_active' => 'boolean',
        'charge_payable' => 'decimal:2',
        'deductible' => 'decimal:2',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function irPeriod()
    {
        return $this->belongsTo(IrPeriod::class, 'ir_period_id');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->end_date) {
            return 'VALID';
        }

        $today = Carbon::today();
        if ($this->end_date->lt($today)) {
            return 'EXPIRED';
        }

        return $this->end_date->lte($today->copy()->addDays(7)) ? 'EXPIRING_SOON' : 'VALID';
    }
}
