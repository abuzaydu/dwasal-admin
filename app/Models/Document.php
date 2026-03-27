<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tanzania VMS - Static legal documents (long-term).
 */
class Document extends Model
{
    protected $fillable = [
        'company_id', 'name', 'category', 'file_path',
        'issue_date', 'expiry_date', 'visibility',
        'vehicle_id', 'driver_id', 'uploaded_by', 'remarks',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public const CATEGORIES = ['vehicle', 'driver', 'business', 'safety'];
    public const VISIBILITY = ['admin_only', 'driver_allowed'];

    // Tanzania - Required vehicle documents for compliance
    public const REQUIRED_VEHICLE_DOCS = [
        'Vehicle Registration Card',
        'Road License',
        'Motor Vehicle Insurance Certificate',
        'Vehicle Inspection Certificate',
    ];

    public const VEHICLE_DOCS = [
        'Vehicle Registration Card', 'Road License',
        'Motor Vehicle Insurance Certificate', 'Vehicle Inspection Certificate',
    ];

    public const DRIVER_DOCS = ['Driving License', 'National ID'];
    public const BUSINESS_DOCS = ['Business License', 'TIN Certificate', 'Company Registration Certificate'];
    public const SAFETY_DOCS = ['Fire Extinguisher Certificate'];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->expiry_date) return 'valid';
        if ($this->expiry_date->isPast()) return 'EXPIRED';
        if ($this->expiry_date->diffInDays(now()) <= 7) return 'EXPIRING_SOON';
        return 'VALID';
    }

    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }
}
