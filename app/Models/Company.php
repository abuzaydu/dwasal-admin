<?php

namespace App\Models;

use App\Models\Badge;
use App\Models\Employee;
use App\Models\Ownership;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Company extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'company_api_key',
    ];

    protected static function booted(): void
    {
        static::creating(function (Company $company) {
            if (empty($company->company_api_key)) {
                $company->company_api_key = self::generateUniqueApiKey();
            }
        });

        static::created(function (Company $company) {
            Ownership::syncDefaultsForCompany((int) $company->id);
        });
    }

    public static function generateUniqueApiKey(): string
    {
        do {
            $key = 'dwc_' . bin2hex(random_bytes(24));
        } while (self::query()->where('company_api_key', $key)->exists());

        return $key;
    }

    public function rotateApiKey(): string
    {
        $this->company_api_key = self::generateUniqueApiKey();
        $this->save();

        return $this->company_api_key;
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
    public function employee()
    {
        return $this->hasMany(Employee::class);
    }
    public function badges()
    {
        return $this->hasMany(Badge::class);
    }
    public function vendor(){
        return $this->hasMany(Vendor::class);
    }
    public function licenseType(){
        return $this->hasMany(LicenseType::class);
    }
}
