<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $guarded=[];

    public function contact(){

        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function location(){

        return $this->belongsTo(Location::class);
    }

    public function subcategory(){

        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function unit(){

        return $this->belongsTo(Unit::class);
    }

    public function lines(){

        return $this->hasMany(TransactionLine::class);
    }

    public function payments(){

        return $this->hasMany(TransactionPayment::class);
    }
    

    public function user(){

        return $this->belongsTo(User::class);
    }

    public function vendor_orders(){

        return $this->hasMany(VendorOrder::class,'transaction_id');
    }
    
    public function delivery(){

        return $this->belongsTo(DeliveryCharge::class,'delivery_id');
    }
    
    public function shipping(){

        return $this->belongsTo(UserAddress::class,'shipping_id');
    }
    
    
    

}
