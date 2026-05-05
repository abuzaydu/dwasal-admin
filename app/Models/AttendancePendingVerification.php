<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePendingVerification extends Model
{
    protected $fillable = [
        'token',
        'company_id',
        'employee_id',
        'verified_by_user_id',
        'qr_data',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];
}
