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
use Illuminate\Support\Facades\Log;

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

    
    public function codeTester(Request $request)
    {
        // লাইভ সার্ভারে নিরাপদে ডিবাগের জন্য সিক্রেট কুয়েরি প্যারামিটার চেক (যেমন: ://yoursite.com)
        // আপনি চাইলে '1' এর জায়গায় যেকোনো সিক্রেট পাসওয়ার্ডও দিতে পারেন
        $shouldLog = $request->has('debug_mode') && $request->query('debug_mode') == '1';

        if ($shouldLog) {
            Log::info('--- LIVE DEBUG: Product Index AJAX Request Started ---', [
                'url' => $request->fullUrl(),
                'inputs' => $request->all(),
                'ip' => $request->ip()
            ]);
        }

        if ($request->ajax()) {
            $q = request('q');
            $brand_id = request('brand_id');
            $category_id = request('category_id');
            $sub_cat_id = request('sub_category_id');
            $shorting = request('shorting');
            $max_price = request('max_price');
            $min_price = request('min_price');

            // Step 1: Base Query initialization
            if ($shouldLog) Log::info('Step 1: Initializing Base Query');

            $query = Product::with('variation')
                ->where('products.is_new', 0)
                ->where('products.status', 1)
                ->where('products.is_ecom', 1)
                ->leftJoin('categories as c', 'c.id', 'products.category_id')
                ->join('variations as v', 'v.product_id', 'products.id')
                ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                ->select(
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.slug',
                    'products.image',
                    'products.category_id',
                    'stock_manage',
                    'products.brand_id',
                    DB::raw('max(v.sell_price) as price'),
                    DB::raw('SUM(ps.qty_available) as qty_available')
                )
                ->groupBy('products.id');

            // Step 2: Sub Category Filter
            if (!empty($sub_cat_id)) {
                if ($shouldLog) Log::info('Step 2: Applying sub_category_id filter', ['sub_category_id' => $sub_cat_id]);
                $query->whereIn('products.sub_category_id', $sub_cat_id);
            }

            // Step 3: Category Filter
            if (!empty($category_id)) {
                if ($shouldLog) Log::info('Step 3: Applying category_id filter', ['category_id' => $category_id]);
                $query->whereIn('products.category_id', $category_id);
            }

            // Step 4: Brand Filter
            if (!empty($brand_id)) {
                if ($shouldLog) Log::info('Step 4: Applying brand_id filter', ['brand_id' => $brand_id]);
                $query->where('products.brand_id', $brand_id);
            }

            // Step 5: Price Range Filter
            if ($request->filled('min_price') && $request->filled('max_price')) {
                if ($shouldLog) Log::info('Step 5: Applying price range havingBetween filter', ['min' => $min_price, 'max' => $max_price]);
                $query->havingBetween(DB::raw('MAX(v.sell_price)'), [
                    $request->min_price,
                    $request->max_price
                ]);
            }

            // Step 6: Search Query Filter
            if (!empty($q)) {
                if ($shouldLog) Log::info('Step 6: Applying search keyword filter', ['search_query' => $q]);
                $query->where(function ($row) use ($q) {
                    $row->where('products.name', 'Like', '%' . $q . '%');
                    $row->orWhere('products.description', 'Like', '%' . $q . '%');
                });
            }

            // Step 7: Sorting
            if (!empty($shorting)) {
                if ($shouldLog) Log::info('Step 7: Applying sorting type', ['shorting_type' => $shorting]);

                if ($shorting == 'desc') {
                    $query->orderBy('products.id', 'desc');
                } else if ($shorting == 'asc') {
                    $query->orderBy('products.id', 'asc');
                } else if ($shorting == 'name') {
                    $query->orderBy('products.name', 'asc');
                } else if ($shorting == 'name_desc') {
                    $query->orderBy('products.name', 'desc');
                } else if ($shorting == 'price_low') {
                    $query->orderBy('v.sell_price', 'asc');
                } else if ($shorting == 'price_high') {
                    $query->orderBy('v.sell_price', 'desc');
                }
            }

            // Step 8: Final Compiled SQL before Execution
            if ($shouldLog) {
                Log::info('Step 8: Final Compiled SQL before Pagination', [
                    'sql' => $query->toSql(),
                    'bindings' => $query->getBindings()
                ]);
            }

            // এক্সিকিউশন টাইম মাপার জন্য সময় কাউন্ট শুরু
            $startTime = microtime(true);

            // Step 9: Query Execution & Pagination
            $items = $query->groupBy('products.id', 'products.name', 'products.type', 'products.slug', 'products.image', 'products.category_id')
                ->where('products.status', 1)
                ->simplePaginate(32);

            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            if ($shouldLog) {
                Log::info('Step 9: Query executed successfully', [
                    'total_items_this_page' => $items->count(),
                    'has_more_pages' => $items->hasMorePages(),
                    'execution_time_ms' => $executionTime . 'ms'
                ]);
            }

            return response()->json([
                'html' => view('products.index_data', compact('items'))->render(),
                'hasMore' => $items->hasMorePages()
            ]);
        }

        // Step 10: Non-AJAX Normal Request
        if ($shouldLog) Log::info('Step 10: Processing Non-AJAX Page Load');

        $brands = Brand::orderBy('name')->where('is_new', 0)->get();
        $cats = Category::where('is_new', 0)->whereNull('parent_id')->get();

        return view('products.index', compact('cats', 'brands'));
    }
}
