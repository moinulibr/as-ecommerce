<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

use App\Models\Setting;
use App\Models\Location;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        $settings = Setting::first();
        $locations = Location::where(['is_new'=>0,'status'=>1])->get();
        
        View::share([
            'info' => $settings,
            'locations' => $locations,
        ]);

    
    }
}
