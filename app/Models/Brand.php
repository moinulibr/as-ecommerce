<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $appends = ['image_url'];
    
    
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url =  env('APP_MAIN_URL').'brands/'.rawurlencode($this->image);
        } else {
            $image_url =  env('APP_MAIN_URL').'img/default.png';
        }
        return $image_url;
    }
    
    public function discount(){
        $now = date('Y-m-d');
        return $this->hasOne(Discount::class,'brand_id','id')
                ->where('status', 1)
                ->whereDate('start', '<=', $now)
                ->whereDate('end', '>=', $now)
                ->orderBy('priority');
    }
    
    public function products(){

        return $this->hasMany(Product::class)
                        ->where('status',1)
                        ->where('is_ecom',1);
    }
    

}
