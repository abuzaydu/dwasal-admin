<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function applications()
    {
        return $this->belongsToMany(Application::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }
}
