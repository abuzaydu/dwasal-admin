<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    use HasFactory;

    protected $table = 'categories';

    protected $fillable = [];

    /**
    * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    */
    public function parent() {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        return $this->hasMany(self::class, 'parent_id','id');
    }

    public function getParentsAttribute()
    {
        $parents = collect([]);

        $parent = $this->parent;

        while(!is_null($parent)) {
            $parents->push($parent);
            $parent = $parent->parent;
        }

        return $parents;
    }
    
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class , 'category_product', 'category_id', 'product_id')->withTimestamps()->orderBy('name', 'asc');;
    }

}
