<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $guarded=[];
    
    protected $appends = ['favicon_url'];
    
    
    public function getFaviconUrlAttribute()
    {
        if (!empty($this->favicon)) {
            $favicon_url =  env('APP_MAIN_URL').'favicon/'.rawurlencode($this->favicon);
        } else {
            $favicon_url =  env('APP_MAIN_URL').'img/default.png';
        }
        return $favicon_url;
    }
    
}
