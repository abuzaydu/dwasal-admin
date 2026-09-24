<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    //
    protected $guarded = ['id'];

    protected $casts = [
        'works_on_weekend' => 'boolean',
    ];
}
