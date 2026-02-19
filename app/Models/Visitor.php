<?php

namespace App\Models;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class,'host_id');
    }
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }
}
