<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers as FCN;
use Illuminate\Support\Facades\Mail;


Route::get('/clear', function(){
    Artisan::call('optimize'); 
    Artisan::call('view:clear'); 
    Artisan::call('cache:clear'); 
    Artisan::call('config:clear');
    Artisan::call('route:clear'); 
    

    // \Artisan::call('make:controller UserPaymentController -r'); 
    
    Mail::raw('This is a raw test email body!', function ($message) {
        $message->to('arifh6267@gmail.com')
                ->subject('Raw Test Email');
    });
     
    dd('ok');
});


Auth::routes();
$disabledAuthRoutes = [
    'login',
    'register',
    'password/reset',
    'password/email',
    'password/confirm',
    'otp-verify',
    'password-update',
];

foreach ($disabledAuthRoutes as $route) {
    Route::any($route, function () {
        return redirect()->route('front.home')->with('error', 'This action is temporarily disabled.');
    });
}


Route::controller(FCN\SocialitLogineController::class)->group(function(){
    $disabledRoutes = ['login', 'auth/google', 'auth/google/callback'];
    foreach ($disabledRoutes as $route) {
        Route::any($route . '/{any?}', function () {
            return redirect()->route('front.home')->with('error', 'This section is temporarily disabled.');
        })->where('any', '.*');
    }
        Route::get('/auth/google','index')->name('socialite.index');
        Route::get('/auth/google/callback','create');
    });
    
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'bn'])) {
        Session::put('locale', $locale);
        App::setLocale($locale);
    }
    return redirect()->back();
});


Route::group(['as'=>'front.'], function() {
    Route::controller(FCN\HomeController::class)->group(function(){
        Route::get('/','home')->name('home');
        Route::get('/about-us','aboutUs')->name('aboutUs');
        Route::get('/contact-us','contactUs')->name('contactUs');
        Route::get('/careers','career')->name('career');
        Route::get('/privacy-policy','privacyPolicy')->name('privacyPolicy');
        Route::get('/term-condition','termCondition')->name('termCondition');
        Route::get('/return-policy','returnPolicy')->name('returnPolicy');
        Route::get('/refund-policy','refundPolicy')->name('refundPolicy');
        Route::get('/faq','faq')->name('faq');
        Route::get('/send-sms','sendSMs')->name('sendSMs');
        Route::post('/contacts','contact')->name('contact');
        Route::get('/support-center','supportCenter')->name('supportCenter');
        Route::get('/payment-methods','paymentMethods')->name('paymentMethods');
        Route::get('/all-brands','allBrands')->name('allBrands');
        Route::get('/become-seller','becomeSeller')->name('becomeSeller');

    });

    Route::controller(FCN\ProductController::class)->group(function(){
        Route::get('/products-list','index')->name('products.index');
        Route::get('/promotions','promotionProduct')->name('products.promotions');
        Route::get('/category','categories')->name('categories');
        Route::get('/c/{slug}','subCategories')->name('subCategories');
        Route::get('/cs/{slug}','subCategories1')->name('subCategories1');
        Route::get('/s/{slug}','subsubCategories')->name('subsubCategories');
        Route::get('/brands','brands')->name('brands');
        Route::get('/discount-products','discountProduct')->name('discountProduct');
        Route::get('/product-show/{slug}','show')->name('products.show');
        Route::get('/relative-product/{id}','relativeProduct')->name('products.relativeProduct');
        
        Route::get('/combo-products','comboProducts')->name('combo_products');
        Route::get('/depart-products','departProduct')->name('departProduct');
        Route::get('/get-trending-products','trendingProduct')->name('trendingProduct');
        Route::get('/get-hotdeal-products','hotdealProduct')->name('hotdealProduct');
        Route::get('/get-recommended-products','recommendedProduct')->name('recommendedProduct');
        Route::get('view-landing-page/{id}','landing_page')->name('landing_pages.view_page');
        Route::get('view-landing-page-two/{id}','landing_pages_two')->name('landing_pages_two.view_page');
        Route::get('/free-shipping-product', 'free_shipping')->name('free-shipping');
        Route::get('/get-variation_price','get_variation_price')->name('get-variation_price');
        Route::get('/get-variation/{id}','getVariation')->name('getVariation');
        
        Route::get('/category-sliders','getSliders')->name('category.sliders');
      
    });



    Route::controller(FCN\AuthController::class)->group(function(){

        $disabledRoutes = ['user-login', 'user-login', 'user-register', 'get-otp'];
        foreach ($disabledRoutes as $route) {
            Route::any($route . '/{any?}', function () {
                return redirect()->route('front.home')->with('error', 'This section is temporarily disabled.');
            })->where('any', '.*');
        }

        Route::get('/user-login','userLogin')->name('userLogin');
        Route::post('/user-login','login')->name('login');
        Route::post('/user-register','Register')->name('register');
        
        Route::get('/get-otp','getOpt')->name('getOpt');
        Route::post('/otp-verify','optVerify')->name('optVerify');
        Route::post('/seller-register-post','sellerRegisterPost')->name('sellerRegisterPost');
    });
    
    Route::resource('checkouts',FCN\CheckoutController::class);
    
    
    Route::group(['middleware' => 'auth'], function() {
        // Route::resource('dashboards',FCN\DashboardController::class);
        // Route::resource('wishlists',FCN\WishlistController::class);
        //Route::resource('product-reviews',FCN\ProductReviewController::class);
        // Route::resource('user-address',FCN\UserAddressController::class);
        // Route::resource('user-payments',FCN\UserPaymentController::class);
        #Route::resource('user-orders',FCN\UserOrderController::class);
    });

    Route::group(['middleware' => 'auth'], function () {
        $disabledRoutes = ['dashboards', 'wishlists', 'user-address', 'user-payments', 'user-orders'];
        foreach ($disabledRoutes as $route) {
            Route::any($route . '/{any?}', function () {
                return redirect()->route('front.home')->with('error', 'This section is temporarily disabled.');
            })->where('any', '.*');
        }

        Route::resource('dashboards', FCN\DashboardController::class);
        Route::resource('wishlists', FCN\WishlistController::class);
        Route::resource('product-reviews', FCN\ProductReviewController::class);
        Route::resource('user-address', FCN\UserAddressController::class);
        Route::resource('user-payments', FCN\UserPaymentController::class);
        Route::resource('user-orders', FCN\UserOrderController::class);
    });





    Route::controller(FCN\UserOrderController::class)->group(function(){
            Route::get('/cancelled-order','cancelledOrder')->name('cancelledOrder');
            Route::get('/return-order','returnOrder')->name('returnOrder');
        });

        Route::controller(FCN\CheckoutController::class)->group(function(){
            Route::get('/sell-payment/{id}','sellPayment')->name('sellPayment');
            Route::post('/sell-payment-store/{id}','sellPaymentStore')->name('sellPaymentStore');
            Route::get('/confirm-order/{id}','confirmOrder')->name('confirmOrder');
            Route::get('/coupon-discount','getCouponDiscount')->name('getCouponDiscount');
            Route::post('/store-session','storeSession')->name('storeSession');


        });
        
        Route::controller(FCN\VendorFollowController::class)->group(function(){
            Route::post('/vendor/follow/{vendorId}', 'toggleFollow');
        });
    

    Route::controller(FCN\DashboardController::class)->group(function(){
        
        Route::get('/confirm-order-landing/{id}','confirmOrderlanding')->name('confirmOrderlanding');
        Route::get('/order-details/{id}','oredrDetails')->name('oredrDetails');
        Route::post('/password-update}','passwordUpdate')->name('passwordUpdate');
        Route::post('/order/cancel-request', 'cancelRequest')->name('order.cancel.request');
    });
    

    Route::resource('carts',FCN\CartController::class);//index method is not using.
    
    
    Route::get('/coupon-discount',[FCN\CheckoutController::class,'getCouponDiscount'])->name('getCouponDiscount');
    Route::get('/cart-cleara-ll',[FCN\CartController::class,'clearAll'])->name('clearAll');
    
    
    
    Route::controller(FCN\ShopController::class)->group(function(){
        Route::get('/shop/{slug}','index')->name('shop');

    });


});
