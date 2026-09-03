<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Variation;

use App\Utils\ProductUtil;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    public $productUtil;
    public function __construct(ProductUtil $productUtil){

        $this->productUtil=$productUtil;
    }


    public function index(){
        return redirect()->route('front.home')->with('error', 'This section is temporarily disabled.');
        $cart = session()->get('cart', []);
        
        // usort($cart, function($a, $b) {
        //     return $a['user_id'] <=> $b['user_id'];
        // });

        $grouped = array_reduce($cart, function($carry, $item) {
            $carry[$item['user_id']][] = $item;
            return $carry;
        }, []);

        if (request()->ajax()) {
            
            $segm='home';
            if(request()->segment){
                $segm=request()->segment;
            }                
            
            $view=view('carts.cart_section')->render();

            return response()->json(['success'=>true,'html'=>$view]);
        }

        $products=Product::with('variation')
                    ->where('products.is_new',0)
                    ->where(function($row){
                        $row->where('is_feature',1)
                            ->orwhere('is_reco',1);

                    })
                    ->Leftjoin('categories as c','c.id','products.category_id')
                    ->join('variations as v','v.product_id','products.id')
                    ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                    ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id','is_feature','is_reco',
                    DB::raw('max(v.sell_price) as price'),
                    DB::raw('SUM(ps.qty_available) as qty_available'))
                    ->groupBy('products.id')
                    ->take(10)
                    ->get();


        return view('carts.index', compact('cart','grouped','products'));
    }
    

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'required|numeric',
            'variation_id' => 'required|numeric',
        ]);

        $url = '';
        if ($request->button && $request->button == 'buy') {
            $url = route('front.checkouts.index');
        }

        $product_id   = $request->product_id;
        $variation_id = $request->variation_id;
        $quantity     = max(1, (int) $request->quantity); // নিশ্চিত হওয়া যাতে সর্বনিম্ন ১ থাকে

        $product   = Product::findOrFail($product_id);
        $variation = Variation::find($variation_id);

        $price          = $product->sell_price;
        $p_price        = getProductDiscount($product);
        $discount_price = $p_price['discount_price'];
        $discount       = $p_price['discount'];

        $cart     = session()->get('cart', []);
        $stock    = $this->productUtil->checkProductStock($product_id, $variation_id, 2);
        $is_stock = $product->stock_manage;

        if ($is_stock == 1 && $stock < $quantity) {
            return response()->json(['success' => false, 'msg' => 'Stock Not Available!']);
        }

        // কার্টে যদি প্রোডাক্টটি অলরেডি থাকে
        if (isset($cart[$variation_id])) {

            // FIX: ইনপুট ফিল্ড থেকে আসা quantity সরাসরি সেট হবে ($quantity > 1 কন্ডিশনের প্রয়োজন নেই)
            $new_stock = $quantity;

            if ($is_stock == 1 && $stock < $new_stock) {
                return response()->json(['success' => false, 'msg' => 'Stock Not Available!']);
            }

            $cart[$variation_id]['quantity']      = $new_stock;
            $cart[$variation_id]['variation_id']  = $variation_id;
            $cart[$variation_id]['price']         = $price - $discount_price;
            $cart[$variation_id]['old_price']     = $price;
            $cart[$variation_id]['discount']      = $discount_price;
            $cart[$variation_id]["discount_id"]   = $discount->id ?? null;
            $cart[$variation_id]['qty_available'] = $product->stocks->sum('qty_available');
        } else {
            // নতুন প্রোডাক্ট কার্টে যোগ হলে
            $cart[$variation_id] = [
                "name"             => $product->name,
                "variation_name"   => $variation->name,
                "quantity"         => $quantity,
                "qty_available"    => $product->stocks->sum('qty_available'),
                "price"            => $price - $discount_price,
                "discount"         => $discount_price,
                "discount_id"       => $discount->id ?? null,
                "old_price"        => $price,
                "variation_id"     => $variation_id,
                "product_id"       => $product_id,
                "image"            => $product->image_url,
                "stock_manage"     => $product->stock_manage,
                "user_id"          => $product->user_id,
                "is_free_shipping" => $product->is_free_shipping
            ];
        }

        session()->put('cart', $cart);

        $view       = view('carts.cart_section')->render();
        $total_item = getTotalCart();

        return response()->json([
            'url'     => $url,
            'html'    => $view,
            'success' => true,
            'msg'     => 'Product added to cart successfully!',
            'item'    => $total_item
        ]);
    }
    /*
        public function store(Request $request){
        
            $data=$request->validate([
                'product_id' => 'required|numeric',
                'variation_id' => 'required|numeric',
            ]);
        
            $url='';
            if($request->button && $request->button=='buy'){
                $url=route('front.checkouts.index');
            }
            $product_id=$request->product_id;
            $variation_id=$request->variation_id;
            $quantity= $request->quantity;


            $product = Product::findOrFail($product_id);
            $variation=Variation::find($variation_id);
            
            $price=$product->sell_price;
            $p_price=getProductDiscount($product);
            $discount_price=$p_price['discount_price'];
            
            $discount=$p_price['discount'];
            
    
            $cart = session()->get('cart', []);
            
            $stock=$this->productUtil->checkProductStock($product_id, $variation_id,2);
            
            $is_stock=$product->stock_manage;
            
            if($is_stock == 1 && $stock <$quantity){
                return response()->json(['success'=>false,'msg'=>' Stock Not Available!']);
            }
                    
    
            if(isset($cart[$variation_id])) {
            
                $old_stock=$cart[$variation_id]['quantity'];
                $new_stock=$old_stock+1;            
                if($quantity>1){
                    $new_stock=$quantity;
                }
                
                if($is_stock == 1 && $stock <$new_stock){
                    return response()->json(['success'=>false,'msg'=>' Stock Note Available!']);
                }
            
                $cart[$variation_id]['quantity']=$new_stock;
                $cart[$variation_id]['variation_id']=$variation_id;
                $cart[$variation_id]['price']=$price - $discount_price;
                $cart[$variation_id]['old_price']=$price;
                $cart[$variation_id]['discount']=$discount_price;
                $cart[$variation_id]["discount_id"]= $discount->id ??null;
                $cart[$variation_id]['qty_available']=$product->stocks->sum('qty_available');
            } else {
                
                $cart[$variation_id] = [
                    "name" => $product->name,
                    "variation_name" => $variation->name,
                    "quantity" => $quantity,
                    "qty_available" => $product->stocks->sum('qty_available'),
                    "price" => $price - $discount_price,
                    "discount" => $discount_price,
                    "discount_id" => $discount->id ??null,
                    "old_price" => $price,
                    "variation_id" => $variation_id,
                    "product_id" => $product_id,
                    "image" => $product->image_url,
                    "stock_manage" => $product->stock_manage,
                    "user_id" => $product->user_id,
                    "is_free_shipping" => $product->is_free_shipping
                ];
            }
            session()->put('cart', $cart);
            $view=view('carts.cart_section')->render();
            $total_item=getTotalCart();
            return response()->json(['url'=>$url,'html'=>$view,'success'=>true,'msg'=>'Product added to cart successfully!','item'=>$total_item]);

        }
    */


    public function edit(Request $request,$id)
    {
        if($id){
            $qty=$request->quantity;
            
            $cart = session()->get('cart');
            
            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }
            
            $segm='home';
            if($request->segment){
                $segm=$request->segment;
            }
          
            if($qty==0){
                if(isset($cart[$id])) {
                    unset($cart[$id]);
                }
            }else{
                $product_id=$cart[$id]["product_id"];
                $is_stock=$cart[$id]["stock_manage"];
                $stock=$this->productUtil->checkProductStock($product_id, $id,2);
                if($is_stock == 1 && $stock <$qty){
                    return response()->json(['success'=>false,'msg'=>' Stock Not Available!']);
                }
                
                $cart[$id]["quantity"] = $qty;
              
            }
           
            session()->put('cart', $cart);
            $view=view('carts.cart_section')->render();
            
            return response()->json(['success'=>true,'msg'=>'Update cart successfully!','html'=>$view,'item'=>getTotalCart()]);
        }else{
            return response()->json(['success'=>false,'msg'=>' Something Went Wrong !']);
        }
    }


    public function destroy($id)
    {
        if($id) {
            
            $cart = session()->get('cart');
            if(isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }

            $view=view('carts.cart_section')->render();
            $total_item=getTotalCart();
            return response()->json([
              'success'=>true,
              'msg'=>'Product removed successfully !',
              'html'=>$view,
              'item'=>$total_item
            ]);
        }else{
            return response()->json(['status'=>false,'msg'=>' Something Went Wrong !']);
        }
    }
  
    public function clearAll(Request $request)
    {  
        if(isset($request->id)){
            
            $id=$request->id;
            $cart = session()->get('cart');
            if(isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
            
        }else{
            session()->put('cart', []);
            session()->put('coupon_discount', []);
            session()->put('coupon_discount', null);
            session()->put('discount_type', null);
            session()->put('coupon_id', null);
        }
        
        $total_item=getTotalCart();
        $view=view('carts.cart_section')->render();
        
        $url='';
        if(isset($request->url)){
            $url=route('front.carts.index');
        }elseif($total_item ==0){
            $url=route('front.home');
        }
        return response()->json([
            'success'=>true,
            'url'=>$url,
            'msg'=>'Product removed successfully !',
            'html'=>$view,
            'item'=>$total_item
            ]);
    }


}
