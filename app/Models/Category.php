<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Product;
use App\Models\Category;
use DB;

class Category extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $appends = ['image_url'];
    
    
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url =  env('APP_MAIN_URL').'categories/'.rawurlencode($this->image);
        } else {
            $image_url =  env('APP_MAIN_URL').'img/default.png';
        }
        return $image_url;
    }
    

    public function products(){

        return $this->hasMany(Product::class)
                        ->where('is_new',0)
                        
                        ->where('status',1)
                        ->where('is_ecom',1);
    }
    
    public function productwithprice(){
        
        return $this->hasMany(Product::class,'products.category_id','id')
                        ->where('products.is_new',0)
                        ->where('products.status', 1)
                        ->where('products.is_ecom', 1)
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id',
                            DB::raw('max(v.sell_price) as price'),
                            DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id');
                        
    }

    public function parent(){

        return $this->belongsTo(Category::class,'parent_id');
    }

    public function subcats(){

        return $this->hasMany(Category::class,'parent_id');
    }
    
    public function discount(){
        $now = date('Y-m-d');
        return $this->hasOne(Discount::class,'category_id','id')
                ->where('status', 1)
                ->whereDate('start', '<=', $now)
                ->whereDate('end', '>=', $now)
                ->orderBy('priority');
    }
    

}
