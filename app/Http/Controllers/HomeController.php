<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Slider;
use App\Models\Category;
use App\Models\Department;
use App\Models\Product;
use App\Models\Brand;
use App\Models\AboutUs;
use App\Models\Contact;
use App\Models\ContactUs;
use App\Models\Page;
use App\Models\Setting;
use App\Models\ProductFeature;
use App\Models\FaqPage;

class HomeController extends Controller
{
    public function sendSMs(){
     
    }


    public function home(){
        $sliders=Slider::where('is_new',0)->whereStatus(1)->latest()->get();
        $brands=Brand::where('is_new',0)->whereisTop(1)->take(12)->get();
        $products=Product::with('variation')
                        ->where('products.is_new',0)
                        ->where('products.status',1)
                        ->where('products.is_ecom',1)
                        ->where(function($row){
                            $row->where('is_feature',1)
                                ->orwhere('is_reco',1);

                        })
                        ->Leftjoin('categories as c','c.id','products.category_id')
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id','is_feature','is_reco','stock_manage','products.brand_id',
                        DB::raw('max(v.sell_price) as price'),
                        DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id')
                        ->take(30)
                        ->get();

        $cats=Category::where('is_new',0)
                    ->whereNull('parent_id')
                    ->get();
                    
        $pcats = Category::where('is_new', 0)
                ->whereNull('parent_id')
                ->where('is_home', 1) // Added this since your Blade file filters by 'is_home'
                ->with(['productwithprice' => function ($query) {
                    $query->take(12); // Limits the loaded products to 12 per category database-side
                }])
                ->get();
        
        $features=ProductFeature::where('is_new',0)->get();
        $departments=[];
        $followedProducts = collect();

        if(auth()->check()) {
            $followedVendorIds = auth()->user()
                ->followingVendors()
                ->pluck('vendor_id');
        
            $followedProducts = Product::with('variation')
                ->whereIn('user_id', $followedVendorIds)
                ->where('status',1)
                ->where('is_ecom',1)
                ->leftJoin('variations as v','v.product_id','products.id')
                ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.slug',
                    'products.image',
                    'products.category_id',
                    'products.brand_id',
                    'products.is_feature',
                    'products.is_reco',
                    'products.stock_manage',
                    DB::raw('MAX(v.sell_price) as price'),
                    DB::raw('SUM(ps.qty_available) as qty_available')
                )
                ->groupBy('products.id')
                ->orderBy('products.created_at', 'desc')
                ->take(8)
                ->get();
        }

        return view('home', compact('sliders','brands','products','departments','cats','features','pcats','followedProducts'));
    }
    
    public function allBrands(){
        $brands = Brand::where('is_new', 0)
            //   ->where('is_top', 1)
               ->paginate(20);
               
        return view('pages.brands', compact('brands'));
    }

    public function shop(){
        $cats=Category::get();
        $brands=Brand::take(12)->get();

        return view('products.index', compact('cats','brands'));
    }
    
    public function contactUs(){
        $setting=Setting::first();
        return view('pages.contact_us', compact('setting'));

    }
    
    public function contact(Request $request){

        $data=$request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        ContactUs::create($data);
        $url=route('front.contactUs');
        return response()->json(['status'=>true,'msg'=>'Your message has been sent successfully!', 'url'=>$url]);

    }
    
    public function becomeSeller(){
        return view('pages.seller_register');
    }
    
    public function aboutUs(){
        $page=Page::where('slug','about-us')->first();
        return view('pages.index', compact('page'));
    }

    
    public function privacyPolicy(){
        $page=Page::where('slug','privacy-policy')->first();
        return view('pages.index', compact('page'));

    }
    
    public function termCondition(){
        $page=Page::where('slug','term-condition')->first();
        return view('pages.index', compact('page'));

    }
    
    public function faq(){
        $items=FaqPage::where('is_new',0)->get();
        return view('pages.faq', compact('items'));
        
        // $page=Page::where('slug','faq')->first();
        // return view('pages.index', compact('page'));
    }

    public function returnPolicy(){
        $page=Page::where('slug','return-policy')->first();
        return view('pages.index', compact('page'));
    }
    
    public function supportCenter(){
        $page=Page::where('slug','support-center')->first();
        return view('pages.index', compact('page'));
    }
    
    public function paymentMethods(){
        $page=Page::where('slug','payment-methods')->first();
        return view('pages.index', compact('page'));
    }
    
    public function refundPolicy(){
        $page=Page::where('slug','refund-policy')->first();
        return view('pages.index', compact('page'));
    }
    

}
