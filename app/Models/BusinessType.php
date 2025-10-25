<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $fillable = [
        'type', 'type_sw', 'type_icon', 'description', 'description_sw',
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }

}
