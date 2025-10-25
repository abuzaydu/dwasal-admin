<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnSale extends Model
{
    protected $fillable = [
        'shop_id', 'user_id', 'customer_id', 'sale_order_id', 'pro_invoice_id', 'currency', 'defcurr', 'ex_rate', 'sale_amount', 'sale_discount', 'sale_amount_paid', 'time_paid', 'tax_amount', 'status', 'pay_type', 'comments', 'time_created', 'sync_id', 'sale_type', 'invoice_no', 'due_date', 'vehicle_no', 'grade_id', 'year',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }


    public function anSaleitems()
    {
        return $this->hasMany(AnSaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function($sale){
            if (!is_null($sale->anSaleitems())) {
               $sale->anSaleitems()->delete();
            }elseif (!is_null($sale->serviceSaleItem())) {
                $sale->serviceSaleItem()->delete();
            }
        });
    }

    public function serviceSaleItem()
    {
        return $this->hasMany(ServiceSaleItem::class);
    }

    public function salePayments()
        {
            return $this->hasMany(SalePayment::class);
        } 
}
