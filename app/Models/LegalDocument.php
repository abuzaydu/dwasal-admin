<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class LegalDocument extends Model
{
    protected $table = 'legal_documents';

    protected $fillable = [
        'company_id',
        'user_id',
        'vehicle_id',
        'document_type_id',
        'last_issue_date',
        'expire_date',
        'charge_paid',
        'commission',
        'doc_attachment',
    ];

    protected $casts = [
        'last_issue_date' => 'date',
        'expire_date' => 'date',
    ];

    // Tanzania: required vehicle documents
    public const REQUIRED_VEHICLE_DOCS = [
        'Vehicle Registration Card',
        'Road License',
        'Vehicle Inspection Certificate',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }

    public function getStatusAttribute(): string
    {
        if (!$this->expire_date) {
            return 'VALID';
        }

        $today = Carbon::today();
        if ($this->expire_date->lt($today)) {
            return 'EXPIRED';
        }

        return $this->expire_date->lte($today->copy()->addDays(7)) ? 'EXPIRING_SOON' : 'VALID';
    }

    public function isExpired(): bool
    {
        return $this->expire_date ? $this->expire_date->isPast() : false;
    }
}
