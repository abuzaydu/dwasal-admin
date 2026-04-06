<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $guarded = [];

    public function refuels()
    {
        return $this->hasMany(Refuel::class);
    }

    public function legalDocuments()
    {
        return $this->hasMany(LegalDocument::class, 'vehicle_id');
    }

    public function insurances()
    {
        return $this->hasMany(Insurance::class, 'vehicle_id');
    }

    /**
     * Tanzania: Vehicle is legal only if required legal docs are valid and insurance is valid.
     */
    public function isVehicleLegal(): bool
    {
        // Ownership_id controls whether required legal documents are mandatory.
        // Ownership_id == 1 => required docs must be present and not expired.
        if ((int) $this->ownership_id === 1) {
            $required = LegalDocument::REQUIRED_VEHICLE_DOCS;
            $docs = $this->legalDocuments()->with('documentType')->get();

            foreach ($required as $requiredName) {
                $doc = $docs->first(function (LegalDocument $d) use ($requiredName) {
                    return $d->documentType?->dt_name === $requiredName;
                });

                if (!$doc || $doc->isExpired()) return false;
            }
        }

        $today = Carbon::today();
        $insurance = $this->insurances()
            ->where('is_active', true)
            ->whereDate('end_date', '>=', $today)
            ->orderByDesc('end_date')
            ->first();

        if (!$insurance) {
            return false;
        }

        return true;
    }
}
