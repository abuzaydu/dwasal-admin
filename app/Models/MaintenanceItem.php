<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceItem extends Model
{
    protected $table = 'maintenance_items';

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class, 'maintenance_id');
    }

    public function partCategory()
    {
        return $this->belongsTo(PartCategory::class, 'part_category_id');
    }

    public function part()
    {
        return $this->belongsTo(Part::class, 'part_id');
    }
}
