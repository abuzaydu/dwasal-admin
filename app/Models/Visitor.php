<?php

namespace App\Models;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Visitor extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    protected $appends = ['visitor_photo_url'];

    public function user()
    {
        return $this->belongsTo(User::class,'host_id');
    }
    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function getVisitorPhotoUrlAttribute(): string
    {
        $default = asset('assets/img/user-icon.webp');
        $photo = (string) ($this->visitor_photo ?? '');

        if ($photo === '') {
            return $default;
        }

        if (Str::startsWith($photo, ['http://', 'https://'])) {
            return $photo;
        }

        $normalized = ltrim($photo, '/');
        if (Str::startsWith($normalized, 'storage/')) {
            return asset($normalized);
        }

        if (Str::startsWith($normalized, 'visitors/')) {
            return asset('storage/' . $normalized);
        }

        return asset('storage/visitors/' . $normalized);
    }
}
