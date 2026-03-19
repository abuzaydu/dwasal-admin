<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VmsExpenseAttachment extends Model
{
    protected $guarded = [];

    public function vmsExpense(){
        return $this->belongsTo(VmsExpense::class);
    }
}
