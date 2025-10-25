<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServCategory extends Model
{
    
    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function services()
    {
        return $this->belongsToMany(Service::class , 'serv_category_service', 'serv_category_id', 'service_id')->withTimestamps()->orderBy('name', 'asc');;
    }
}
