<?php

namespace App\Models;

use App\Models\Company;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    //
        protected $guarded = [];

        protected $attribute = [
            'status' => 'available' //default for every badge created
        ];
    
        public function company()
        {
            return $this->belongsTo(Company::class);
        }
        public function visitors()
        {
            return $this->hasMany(Visitor::class);
        }
}
