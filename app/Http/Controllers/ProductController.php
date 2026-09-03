<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Combo;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Information;
use App\Models\LandingPage;
use App\Models\DeliveryCharge;
use App\Models\Variation;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function getSliders()
    {
        $pcats = Category::where('is_new', 0)
                ->whereNull('parent_id')
                ->where('is_home', 1) // Added this since your Blade file filters by 'is_home'
                ->with(['productwithprice' => function ($query) {
                    $query->take(12); // Limits the loaded products to 12 per category database-side
                }])
                ->get();
                
        $html = view('products.category_sliders_data', compact('pcats'))->render();
    
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }


    public function index(Request $request)
    {
        if ($request->ajax()) {

            $q           = $request->input('q');
            $brand_id    = $request->input('brand_id');
            $category_id = $request->input('category_id');
            $sub_cat_id  = $request->input('sub_category_id');
            $shorting    = $request->input('shorting');
            $min_price   = $request->input('min_price');
            $max_price   = $request->input('max_price');

            // Base Query Build
            $query = Product::query()
                ->select([
                    'products.id',
                    'products.name',
                    'products.type',
                    'products.slug',
                    'products.image',
                    'products.category_id',
                    'products.sub_category_id',
                    'products.brand_id',
                    'products.stock_manage',
                    DB::raw('COALESCE(MAX(v.sell_price), 0) as price'),
                    // Safe subquery for total available stock across locations
                    DB::raw('(SELECT COALESCE(SUM(qty_available), 0) FROM product_stocks WHERE product_stocks.product_id = products.id) as qty_available')
                ])
                ->leftJoin('variations as v', 'v.product_id', '=', 'products.id')
                ->where('products.is_new', 0)
                ->where('products.status', 1)
                ->where('products.is_ecom', 1);

            // Filter: Sub Category
            if (!empty($sub_cat_id)) {
                $query->whereIn('products.sub_category_id', (array) $sub_cat_id);
            }

            // Filter: Category
            if (!empty($category_id)) {
                $query->whereIn('products.category_id', (array) $category_id);
            }

            // Filter: Brand
            if (!empty($brand_id)) {
                $query->where('products.brand_id', $brand_id);
            }

            // Filter: Search Keyword
            if (!empty($q)) {
                $query->where(function ($row) use ($q) {
                    $row->where('products.name', 'LIKE', '%' . $q . '%')
                        ->orWhere('products.description', 'LIKE', '%' . $q . '%');
                });
            }

            // Single & Complete Group By Matching SELECT Non-Aggregated Columns
            $query->groupBy(
                'products.id',
                'products.name',
                'products.type',
                'products.slug',
                'products.image',
                'products.category_id',
                'products.sub_category_id',
                'products.brand_id',
                'products.stock_manage'
            );

            // Filter: Price Range (HAVING Clause for Aggregate Column)
            if ($request->filled('min_price') && $request->filled('max_price')) {
                $query->having(DB::raw('MAX(v.sell_price)'), '>=', (float) $min_price)
                    ->having(DB::raw('MAX(v.sell_price)'), '<=', (float) $max_price);
            }

            // Sorting Logic
            if (!empty($shorting)) {
                switch ($shorting) {
                    case 'asc':
                        $query->orderBy('products.id', 'asc');
                        break;
                    case 'name':
                        $query->orderBy('products.name', 'asc');
                        break;
                    case 'name_desc':
                        $query->orderBy('products.name', 'desc');
                        break;
                    case 'price_low':
                        $query->orderBy(DB::raw('MAX(v.sell_price)'), 'asc');
                        break;
                    case 'price_high':
                        $query->orderBy(DB::raw('MAX(v.sell_price)'), 'desc');
                        break;
                    case 'desc':
                    default:
                        $query->orderBy('products.id', 'desc');
                        break;
                }
            } else {
                $query->orderBy('products.id', 'desc');
            }

            // Execute Simple Pagination
            $items = $query->simplePaginate(32);

            return response()->json([
                'html'    => view('products.index_data', compact('items'))->render(),
                'hasMore' => $items->hasMorePages()
            ]);
        }

        // Page Initial Load Data
        $brands = Brand::orderBy('name')->where('is_new', 0)->get();
        $cats   = Category::where('is_new', 0)->whereNull('parent_id')->get();

        return view('products.index', compact('cats', 'brands'));
        
        /*
            if ($request->ajax()) {

                $category_id = request('category_id');
                $sub_cat_id  = request('sub_category_id');
                $brand_id    = request('brand_id');

                // DEBUG TEST 2: Checking Request Inputs & Progressive Query Steps
                $query = DB::table('products')
                    ->where('category_id', 41)
                    ->where('status', 1)
                    ->where('is_ecom', 1)
                    ->where('is_new', 0);

                $step1_base_count = (clone $query)->count();

                // Join with Variations
                $query->leftJoin('variations as v', 'v.product_id', '=', 'products.id');
                $step2_join_variation_count = (clone $query)->count();

                // Group By Check
                $step3_grouped_data = (clone $query)
                    ->select('products.id', DB::raw('MAX(v.sell_price) as price'))
                    ->groupBy('products.id')
                    ->get();

                return response()->json([
                    'request_inputs' => [
                        'category_id_from_request' => $category_id,
                        'sub_category_id_from_request' => $sub_cat_id,
                        'brand_id_from_request' => $brand_id,
                        'min_price' => request('min_price'),
                        'max_price' => request('max_price'),
                        'shorting' => request('shorting'),
                    ],
                    'debug_steps' => [
                        'step1_base_count' => $step1_base_count,
                        'step2_join_variation_count' => $step2_join_variation_count,
                        'step3_grouped_count' => $step3_grouped_data->count(),
                        'step3_data' => $step3_grouped_data
                    ]
                ]);
            }
        */
    }

    public function indexold(Request $request){
        $shouldLog = 1;//$request->has('debug_mode') && $request->query('debug_mode') == '1';

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
    
    
    public function promotionProduct(Request $request){
        if ($request->ajax()) {

            $q=request('q');
            $brand_id=request('brand_id');
            $category_id=request('category_id');

            $sub_cat_id=request('sub_cat_id');
            $shorting=request('shorting');
            $max_price=request('max_price');
            $min_price=request('min_price');
            $now=date('Y-m-d');
            $query=Product::with('variation')
                        ->where(function ($query) use ($now) {
                            // 1. Check if the product is directly linked via discount_products
                            $query->whereHas('discounts', function ($q) use ($now) {
                                $q->where('status', 1)
                                  ->where('start', '<=', $now)
                                  ->where('end', '>=', $now);
                            })
                            // 2. OR check if the product's brand_id is in the discount table
                            ->orWhereHas('brandDiscounts', function ($q) use ($now) {
                                $q->where('status', 1)
                                  ->where('start', '<=', $now)
                                  ->where('end', '>=', $now);
                            })
                            // 3. OR check if the product's category_id is in the discount table
                            ->orWhereHas('categoryDiscounts', function ($q) use ($now) {
                                $q->where('status', 1)
                                  ->where('start', '<=', $now)
                                  ->where('end', '>=', $now);
                            });
                        })
                        ->where('products.is_new',0)
                        ->where('products.status',1)
                        ->where('products.is_ecom',1)
                        ->Leftjoin('categories as c','c.id','products.category_id')
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id','stock_manage','products.brand_id',
                        DB::raw('max(v.sell_price) as price'),
                        DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id'); 
            if(!empty($sub_cat_id)){
                $query->whereIn('products.sub_category_id',$sub_cat_id);
            } 

            if(!empty($category_id)){
                $query->whereIn('products.category_id',$category_id);
            } 

            if(!empty($brand_id)){
                $query->where('products.brand_id',$brand_id);
            } 

     

            if(!empty($max_price) && !empty($min_price)){
                $query->whereBetween('products.sell_price', [$min_price, $max_price]);
            } 

            
            if(!empty($q)){
                $query->where(function($row) use ($q){
                    $row->where('products.name','Like','%'.$q.'%');
                    $row->orwhere('products.description','Like','%'.$q.'%');
                });
            }

            if(!empty($shorting)){

                if ($shorting=='desc') {
                    $query->orderBy('products.id', 'desc');
                }else if ($shorting=='asc') {
                    $query->orderBy('products.id', 'asc');
                }else if ($shorting=='name') {
                    $query->orderBy('products.name', 'asc');
                }else if ($shorting=='name_desc') {
                    $query->orderBy('products.name', 'desc');
                }else if ($shorting=='price_low') {
                    $query->orderBy('v.sell_price', 'asc');
                }else if ($shorting=='price_high') {
                    $query->orderBy('v.sell_price', 'desc');
                }
                
            } 

            $items=$query->groupBy('products.id','products.name','products.type','products.slug','products.image','products.category_id')
            ->where('products.status', 1)
            ->paginate(30);

            return view('products.index_data', compact('items'))->render(); 
        }


        $brands=Brand::whereHas('discount')->orderBy('name')->where('is_new',0)->get();
        $cats=Category::whereHas('discount')->where('is_new',0)->whereNull('parent_id')->get();
        return view('products.promotions', compact('cats','brands'));
    }
    
    
    public function comboProducts (){
        
        $items=Combo::with('product')->paginate(30);
        return view('frontend.products.combo', compact('items'));
        
    }

    public function show($slug)
    {
        $product = Product::with('variations','user','user.vendorAddress')
                  ->where('slug', $slug)
                  ->firstOrFail();
                  
    
        
        if(empty($product)){
            return redirect('/shop');
        }
        $id=$product->id;
        $recent_product = session()->get('recent_product', []);
  
        if(!in_array($id,$recent_product)) {
           array_push($recent_product,$id);
           session()->put('recent_product', $recent_product);
        } 

        
        $products=Product::with('variation')
                        ->where('products.is_new',0)
                        ->where('products.id','!=',$id)
                        ->where('products.category_id', $product->category_id)
                        ->Leftjoin('categories as c','c.id','products.category_id')
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id','is_feature','is_reco','stock_manage',
                        DB::raw('max(v.sell_price) as price'),
                        DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id')
                        ->take(16)
                        ->get();

        // $array=[];
        // if ($product->variants) {
        //     $array=json_decode($product->variants,true);
        // }

        // foreach ($array as $sskey => $value) {
        //     foreach ($value as $key => $nvalue) {
        //            dd($key); 
        //        }   
        // }
        // dd($array);   
        return view('products.show', compact('product','products'));
    }
  
    public function relativeProduct($id){

        $product = Product::with('sizes','sizes.stocks')->find($id);

        $products=Product::with('variation')
                
                ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','stock_manage','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->where('products.category_id', $product->category_id)
                ->whereNotIn('products.id', [$id])
                ->where('status', 1)
                ->take(12)
                ->get();
        $view=view('frontend.products.partials.relative_product', compact('products'))->render();

        return response()->json(['success'=>true,'html'=>$view]);

    }

    public function trendingProduct(){
      
       $info = Information::first();
       $newarrival_num = $info->newarrival_num; 
        $products=Product::with('variation')
                ->whereNull('products.discount_type')
                ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','stock_manage','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->where('status', 1)
                ->latest()
                ->take($newarrival_num)
                ->get();

        $view=view('frontend.products.partials.trending_product', compact('products'))->render();
        return response()->json(['success'=>true,'html'=>$view]);
    }

    public function hotdealProduct(){
      
      $info = Information::first();
        $discount_num = $info->discount_num;  
      
        $products=Product::with('variation')
                ->whereNotNull('products.discount_type')
                ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','is_stock','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->where('status', 1)
                ->take($discount_num)->get();
        $view=view('frontend.products.partials.hotdeal_product', compact('products'))->render();
        return response()->json(['success'=>true,'html'=>$view]);
    }

    public function recommendedProduct(){
      
        $info = Information::first();
        $recom_num = $info->recommend_num;   

        $products=Product::with('variation')
                ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','is_stock','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->where('status', 1)
                ->where('is_recommended', 1)
                ->take($recom_num)
                ->get();
        $view=view('frontend.products.partials.recommended_product', compact('products'))->render();
        return response()->json(['success'=>true,'html'=>$view]);
    }

    public function discountProduct(Request $request){

            if ($request->ajax()) {
                $items=Product::with('variation')
                    ->whereNotNull('products.discount_type')
                    ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','is_stock','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                    ->where('status', 1)
                    ->latest()
                    ->paginate(24);
                $view=view('frontend.products.partials.discount', compact('items'))->render();

                return response()->json(['success'=>true,'html'=>$view]);
            }
        return view('frontend.products.discount');
    }

    public function brands(){
        $items=Brand::orderBy('name')->get();
        return view('frontend.brands', compact('items'));
    }
    
     public function landing_page($id)
    {
        
        $ln_pg = LandingPage::with('images')->find($id);
        $title = $ln_pg->title1;
        $charges=DeliveryCharge::whereNotNull('status')->get();
        return view('backend.landing_pages.land_page', compact('ln_pg','charges','title'));
    }
    
    public function landing_pages_two($id) {
        $ln_pg = LandingPage::with('images')->find($id);
        $title = $ln_pg->title1;
        $charges=DeliveryCharge::whereNotNull('status')->get();
        return view('backend.landing_pages.land_page_two', compact('ln_pg','charges','title'));
    }
    
    public function subCategories($slug){
    
        $cat=Category::where('slug',$slug)->first();
        $query=Category::whereNotNull('parent_id');
                    if($cat){ 
                        $query->where('parent_id', $cat->id);
                    }
        $subs=$query->get();

        return view('frontend.sub_categories', compact('subs'));
    }
    
    public function subCategories1($slug){
        $cat=Category::with('products')->where('url',$slug)->first();
        $cat_id = $cat['id'];    
        $types=Brand::orderBy('name')->get();
        $cats=Category::whereNull('parent_id')->get();
        $sizes=Size::all();
       
      
       return view('frontend.category_products',compact('types','cats','sizes','cat'));
    }

    public function subsubCategories($slug){
      
       $s_cat=Category::where('url',$slug)->first();
       $scat_id = $s_cat['id']; 
      
       $items =  Product::with('variation')->where('sub_category_id', $scat_id)->where('status', 1)->paginate(30);    
       $types=Brand::orderBy('name')->get();
       $cats=Category::whereNull('parent_id')->get();
       $sizes=Size::all();
      
       return view('frontend.products.another_sub_index',compact('items','types','cats','sizes','s_cat'));
    }
    
    public function categories(){
        $category_id=request('category_id');
        $cats=Category::whereNull('parent_id')->get();
        $query=Category::whereNotNull('parent_id');
                if(!empty($category_id)){
                    $query->where('parent_id',$category_id);
                }
        $subs=$query->get();

        return view('frontend.categories', compact('cats','subs'));
    }
    
    public function free_shipping() {
        
        $items=Product::with('variation')
                ->where('is_free_shipping', 1)
                ->select('products.id','products.name','products.is_free_shipping','products.type','products.purchase_price','products.regular_price','products.sell_price','products.image','is_stock','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->latest()
                ->get();

        return view('frontend.products.free_shipping_products', compact('items'));
        
    }
    
    // Get The Price Of Variation Product
    
    public function get_variation_price(Request $request){
        $data = Product::find($request->product_id);
        $discount_amount = (int)$data->dicount_amount;
        $discount_type = $data->discount_type;
        
        return response()->json([
            'success' => true,
            'discount_amount' => $discount_amount,
            'discount_type' => $discount_type
        ]);
    }

    public function getVariation($id){

        $product = Product::find($id);

        $name='';

        $size=request('size');
        $color=request('color');

        $vquery=Variation::where('product_id', $id);

        if($color){
            if($name){
                $name.='-'.$color;
            }else{
                $name.=$color;
            }
        }

        if($size){
            if($name){
                $name.='-'.$size;
            }else{
                $name.=$size;
            }
            
        }

        

        $vquery->where('name', $name);
        $variation=$vquery->first();


        return response()->json(['variation'=>$variation]);
    }

    public function departProduct(){

        $id=request('id');
        $department=Department::find($id);

        $ids=json_decode($department->products, true);

        $products=Product::with('variation')
                ->select('products.id','products.name','products.type','products.regular_price','products.sell_price','products.image','products.category_id','products.discount_type','products.discount','products.after_discount','products.dicount_amount')
                ->whereIn('products.id', $ids)
                ->where('status', 1)
                ->get();
        $view=view('frontend.products.partials.department_product', compact('products'))->render();
        return response()->json(['success'=>true,'html'=>$view]);


    }


    
}
