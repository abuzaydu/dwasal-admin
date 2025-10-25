<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
