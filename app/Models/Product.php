<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;
use App\Models\Size;
use App\Models\ProductStock;
use App\Models\ProductImage;

use App\Models\Variation;
use App\Models\User;
use App\Models\ProductReview;

class Product extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $appends = ['image_url'];
    
    
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url =  env('APP_MAIN_URL').'products/'.rawurlencode($this->image);
        } else {
            $image_url =  env('APP_MAIN_URL').'img/default.png';
        }
        return $image_url;
    }

    public function category(){

        return $this->belongsTo(Category::class);
    }
  
    public function user(){

        return $this->belongsTo(User::class);
    }



    public function brand() {

        return $this->belongsTo(Brand::class);
    }


    public function stocks() {

        return $this->hasMany(ProductStock::class);
    }

    public function images() {

        return $this->hasMany(ProductImage::class);
    }


    public function variations() {

        return $this->hasMany(Variation::class,'product_id');
    }
    
    public function reviews() {

        return $this->hasMany(ProductReview::class,'product_id');
    }

    public function variation() {

        return $this->belongsTo(Variation::class,'id','product_id')->orderBy('id');
    }
    
    
    // Direct link via the pivot table
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_products', 'product_id', 'discount_id');
    }
    
    // Discount via Brand ID match
    public function brandDiscounts()
    {
        return $this->hasMany(Discount::class, 'brand_id', 'brand_id');
    }
    
    // Discount via Category ID match
    public function categoryDiscounts()
    {
        return $this->hasMany(Discount::class, 'category_id', 'category_id');
    }


}
