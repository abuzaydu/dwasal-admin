<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mail;
use App\Mail\QuoteMail;

class QuoteRequest extends Model
{
    use HasFactory;

    public $fillable = ['name', 'email', 'phone', 'address', 'product', 'message','status','processed_by'];

    /**
     * Write code on Method
     *
     * @return response()
     */
    public static function boot() 
    {
        parent::boot();
        static::created(function ($item) {
            $adminEmail = "admin@dwasal.co.tz";
            Mail::to($adminEmail)->send(new QuoteMail($item));
        });
    }
}
