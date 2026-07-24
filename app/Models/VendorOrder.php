<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorOrder extends Model
{
    protected $guarded=['id'];
    public function user(){

        return $this->belongsTo(User::class,'vendor_id');
    }

    public function lines(){

        return $this->hasMany(TransactionLine::class,'vendor_order_id');
    }


}
