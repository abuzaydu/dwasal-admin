<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Company extends Model
{
   
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
}
