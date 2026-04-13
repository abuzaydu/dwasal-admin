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

    protected static function booted(): void
    {
        static::created(function (Company $company) {
            Ownership::syncDefaultsForCompany((int) $company->id);
        });
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
