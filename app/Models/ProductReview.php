<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $guarded=[];

    protected $appends = ['image_url'];
    
    
    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            $image_url =  asset('reviews/'.rawurlencode($this->image));
        } else {
            $image_url =  env('APP_MAIN_URL').'img/default.png';
        }
        return $image_url;
    }

    public function user() {

        return $this->belongsTo(User::class);
    }
}
