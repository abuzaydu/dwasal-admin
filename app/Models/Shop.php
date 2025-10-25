<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public function company()
    {
      return $this->belongsTo(Company::class);
    }
    
    public function users()
    {
      return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function customers()
    {
      return $this->hasMany(Customer::class);
    }

    public function sales()
    {
      return $this->hasMany(AnSale::class);
    }

    public function costs()
    {
        return $this->hasMany(AnCost::class);
    }
    

    public function categories()
    {
      return $this->hasMany(Category::class)->with(['children']);
    }

    public function products()
    {
        return $this->hasMany(Product::class)->orderBy('name', 'asc');
    }

    public function invoices()
    {
      return $this->hasMany(Invoice::class);
    }

    public function suppliers()
    {
      return $this->hasMany(Supplier::class);
    }

    public function businessType()
    {
      return $this->belongsTo(BusinessType::class);
    }

    public function subscriptionType()
    {
      return $this->belongsTo(SubscriptionType::class);
    }


    public function services()
    {
      return $this->hasMany(Service::class)->orderBy('name', 'asc');
    }
    public function rawMaterials()
    {
      return $this->belongsToMany(RawMaterial::class)->orderBy('name', 'asc');
    }


    public function packingMaterials()
    {
      return $this->belongsToMany(PackingMaterial::class)->orderBy('name', 'asc');
    }


    public function mroItems()
    {
      return $this->hasMany(MROItem::class);
                        
    }


    public function mro()
    {
      return $this->hasMany(Mro::class);
    }

    public function mroUse(){
        return $this->hasMany(MroUse::class);
    }

    public function rmuse(){
      return $this->hasMany(RmUse::class);
    }

    public function pmuse(){
      return $this->hasMany(PmUse::class);
    }

    public function stores()
    {
      return $this->hasMany(Store::class);
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.Shop.' . $this->id;
    }

     public function bankDetails()
    {
        return $this->hasMany(BankDetail::class);
    }

    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }
}
