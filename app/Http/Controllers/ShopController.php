<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;

class ShopController extends Controller
{
    public function index(Request $request, $slug){

        // ===============================
        // GET SHOP
        // ===============================
        $shop = DB::table('vendor_addresses')
            ->join('users', 'vendor_addresses.user_id', '=', 'users.id')
            ->where('vendor_addresses.slug', $slug)
            ->select('vendor_addresses.*','users.image')
            ->first();

        if (!$shop) {
            abort(404);
        }

        $vendorId = $shop->user_id;

        // ===============================
        // PRODUCT BASE QUERY
        // ===============================
        
        $query=Product::with('variation')
                        ->where('products.is_new',0)
                        ->where('products.status',1)
                        ->where('products.is_ecom',1)
                        ->where('products.user_id', $vendorId)
                        ->Leftjoin('categories as c','c.id','products.category_id')
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id','stock_manage','products.brand_id',
                        DB::raw('max(v.sell_price) as price'),
                        DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id'); 


        // ===============================
        // FILTERS (Search / Category / Brand / Price)
        // ===============================

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function($row) use ($q){
                $row->where('products.name','like','%'.$q.'%')
                    ->orWhere('products.description','like','%'.$q.'%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereIn('products.category_id', $request->category_id);
        }

        if ($request->filled('sub_cat_id')) {
            $query->whereIn('products.sub_category_id', $request->sub_cat_id);
        }

        if ($request->filled('brand_id')) {
            $query->where('products.brand_id', $request->brand_id);
        }

        if ($request->filled('min_price') && $request->filled('max_price')) {
            $query->havingBetween(DB::raw('MIN(v.sell_price)'), [
                $request->min_price,
                $request->max_price
            ]);
        }

        // ===============================
        // SORTING
        // ===============================

        switch ($request->shorting) {
            case 'asc':
                $query->orderBy('products.id', 'asc');
                break;

            case 'price_low':
                $query->orderByRaw('MIN(v.sell_price) asc');
                break;

            case 'price_high':
                $query->orderByRaw('MIN(v.sell_price) desc');
                break;

            case 'name':
                $query->orderBy('products.name', 'asc');
                break;

            case 'name_desc':
                $query->orderBy('products.name', 'desc');
                break;

            default:
                $query->orderBy('products.created_at', 'desc');
                break;
        }

        // ===============================
        // PAGINATION (ALWAYS PAGINATOR)
        // ===============================
        $products = $query->paginate(12)->withQueryString();

        // ===============================
        // AJAX RETURN
        // ===============================
        if ($request->ajax()) {
            return view('pages.vendor_shop_products', compact('products'))->render();
        }

        // ===============================
        // REVIEWS
        // ===============================
        $reviewData = DB::table('product_reviews')
            ->join('products', 'products.id', '=', 'product_reviews.product_id')
            ->where('products.user_id', $vendorId)
            ->select(
                DB::raw('COUNT(product_reviews.id) as total_reviews'),
                DB::raw('AVG(product_reviews.review) as average_rating')
            )
            ->first();

        $totalReviews = $reviewData->total_reviews ?? 0;
        $averageRating = number_format($reviewData->average_rating ?? 0, 1);

        // ===============================
        // FOLLOWERS
        // ===============================
        $followerCount = DB::table('vendor_followers')
            ->where('vendor_id', $vendorId)
            ->count();

        $isFollowing = auth()->check()
            ? DB::table('vendor_followers')
                ->where('vendor_id', $vendorId)
                ->where('customer_id', auth()->id())
                ->exists()
            : false;

        // ===============================
        // BRANDS & CATEGORIES
        // ===============================
        $brands = Brand::where('is_new',0)->orderBy('name')->get();
        $cats = Category::where('is_new',0)
                    ->whereNull('parent_id')
                    ->with('subcats')
                    ->get();

        return view('pages.vendor_shop', compact(
            'shop',
            'products',
            'totalReviews',
            'averageRating',
            'isFollowing',
            'followerCount',
            'brands',
            'cats'
        ));
    }
}
