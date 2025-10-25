<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItemTemp extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id', 'shop_id', 'product_id', 'quantity', 'cost_per_unit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
