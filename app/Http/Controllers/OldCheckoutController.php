<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Utils\ProductUtil;
use App\Utils\Util;
use App\Models\Coupon;
use App\Models\User;
use App\Models\Product;
use App\Models\Variation;
use App\Models\DeliveryCharge as Charge;
use App\Models\UserAddress;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\VendorOrder;
use Illuminate\Support\Facades\Auth;


class CheckoutController extends Controller
{
    public $productUtil;
    public function __construct(ProductUtil $productUtil){

        $this->productUtil=$productUtil;
    }
    
    public function index(Request $request){
        $cart = session()->get('cart', []);
        $coupon=session()->get('coupon_discount');
        $coupn_item=Coupon::where('amount', $coupon)->first();
        $cdiscount=getCouponDiscount();

        if($coupn_item && $coupon && ($coupn_item->minimum_amount > getTotalAmount())) {
            session()->put('coupon_discount',null);
            session()->put('discount_type',null);
        
        }

        if($request->ajax()){

            $user_id=Auth::id();
        
            // $charges=DeliveryCharge::whereNotNull('status')->get();
            $charges=Charge::whereStatus(1)->whereIsNew(0)->get();
            $address=UserAddress::whereUserId($user_id)->get();

            $delivery_id=session()->get('delivery_id');
            $shipping_id=session()->get('shipping_id');

            
            return view('checkouts.data', compact('cart','charges','address','cdiscount','shipping_id','delivery_id'));

        }

        if (empty($cart)) {
            return redirect()->route('front.home');
        }
        return view('checkouts.index');
    }
    
     
    public function store(Request $request)
    {

        $data=$request->validate([
            'shipping_id' => 'required',
            'note' => '',
            'delivery_id' => 'required|numeric',
        ],[
            'shipping_id.required'=>'Please add or select a shipping address to continue.',    
            'delivery_id.required'=>'Please Select A Delivery Method',    
        ]);

        $location_id=2;
        
    
        $cart_discount=0;
            
                
        $cdiscount=getCouponDiscount();
        $data['user_id']=Auth::id();
        $data['contact_id']=Auth::user()->contact_id;
        $charge=Charge::find($data['delivery_id']);
        $charge=$charge?$charge->amount:0;

        $total_charge=$charge* totalVendorCart();
        $data['transaction_date']=date('Y-m-d');

        $data['invoice_no']=rand(111111,999999);
        $data['shipping_charge']= $total_charge;
        $data['discount_amount']= $cdiscount + getCartDiscount();
        $data['cal_discount']= $cdiscount + getCartDiscount();
        $data['discount_type']= 'fixed';
        $data['coupon_id']= session()->get('coupon_id');
        $data['is_new']= 0;
        $data['mail_notification']= 1;
        $data['sms_notification']= 1;
        $data['location_id']= $location_id;
        $data['type']= 'sell';
        $data['final_amount']=getTotalAmount()+$total_charge - $cdiscount;
        $data['sub_total']=getTotalAmount() + $cdiscount + getCartDiscount();

        DB::beginTransaction();
               

            $sell=Transaction::create($data);
            
            $carts = session()->get('cart', []);

            $vcarts = array_reduce($carts, function($carry, $item) {
                $carry[$item['user_id']][] = $item;
                return $carry;
            }, []);


            
            foreach ($vcarts as $vendor_id=>$vcart) {

                $vendororder=VendorOrder::create([
                    'transaction_id'=>$sell->id,
                    'vendor_id'=>$vendor_id,
                    'discount_type'=>'fixed',
                    'shipping_charge'=>$charge,
                    'invoice_no'=>rand(111111,999999),
                    'final_amount'=>getTotalAmount($vendor_id) +$charge,
                    'sub_total'=>getTotalAmount($vendor_id) +getCartDiscount($vendor_id),
                    'discount_amount'=>getCartDiscount($vendor_id),
                ]);
                foreach ($vcart as $cart) {
                    $variation_id=$cart['variation_id'];
                    $product_id=$cart['product_id'];
                    $qty=$cart['quantity'];
                    
                    $cart_discount +=$cart['discount'] * $cart['quantity'];
                    
                    $line_data[]=[
                        'product_id'=>$product_id,
                        'variation_id'=>$variation_id,
                        'vendor_order_id'=>$vendororder->id,
                        'quantity'=>$qty,
                        'price'=>$cart['price'],
                        'old_price'=>$cart['old_price'],
                        'discount'=>$cart['discount'],
                        'discount_id'=>$cart['discount_id'],
                    ];
                    
                    $is_stock=$cart["stock_manage"];
                    if($is_stock){
                        $this->productUtil->decreaseProductStock($product_id,$variation_id, $location_id,$qty); 
                    }
                                    
                    
                    
                }
            }

            if (!empty($line_data)) {
                $sell->lines()->createMany($line_data);
            }
            
            session()->put('cart',[]);
            session()->put('coupon_discount',null);
            session()->put('discount_type',null);
            session()->put('coupon_id',null);
            
            
            
            try {
                $this->productUtil->sendNotification($sell);
            } catch (Throwable $e) {
                Log::error('Mail failed to send: ' . $e->getMessage());
            }

            
            DB::commit();
            $url=route('front.sellPayment',[$sell->id]);
            return response()->json([
                'success' => true,
                'url' => $url,
                'msg'    => 'Checkout Successfully..!!'
            ]);
            
        
    }

    public function sellPayment($id){

        $sell=Transaction::find($id);

        return view('checkouts.payment', compact('sell'));
    }

    public function sellPaymentStore($id){
        
        $data=request()->validate([
            'payment_method' => 'required',
        ],[
            'payment_method.required'=>'Please Select A Payment Method',    
        ]);
        
        $sell=Transaction::find($id);
        $sell->payment_method=request('payment_method');
        $sell->save();

        $url=route('front.confirmOrder',[$sell->id]);
        return response()->json([
            'success' => true,
            'url' => $url,
            'msg'    => 'Payment Method Addedd..!!'
        ]);

    }

    public function confirmOrder($id){

        $sell=Transaction::find($id);
        $products=Product::with('variation')
                        ->where('products.is_new',0)
                        ->Leftjoin('categories as c','c.id','products.category_id')
                        ->join('variations as v','v.product_id','products.id')
                        ->leftJoin('product_stocks as ps', 'ps.product_id', 'products.id')
                        ->select('products.id','products.name','products.type','products.slug','products.image','products.category_id',
                        DB::raw('max(v.sell_price) as price'),
                        DB::raw('SUM(ps.qty_available) as qty_available'))
                        ->groupBy('products.id')->take(12)->get();

        return view('checkouts.thank_you', compact('sell','products'));
    }
  
   
    public function getCouponDiscount(Request $request){
        
        $data=$request->validate([
            'code' => 'required'
        ]);
        
        $cart = session()->get('cart');
        $total=0;
        if($cart){
            foreach($cart as $id=>$item){
                $total +=$item['price'] * $item['quantity'];
            }
        }
        
        $item=Coupon::where('code',$request->code)
                    ->where(function($row) use($total){
                        $row->where('minimum_amount','0')
                            ->orWhereNull('minimum_amount')
                            ->orWhere('minimum_amount','<=',$total);
                    })
                    ->whereDate('start','<=', date('Y-m-d'))
                    ->whereDate('end','>=', date('Y-m-d'))->first();
        
        if($item){
            session()->put('coupon_discount', $item->amount);
            session()->put('discount_type', $item->discount_type);
            session()->put('coupon_id', $item->id);
            return response()->json(['success'=>true,'msg'=>'You Got Coupon Discount!']);
        }else{
            return response()->json(['success'=>false,'msg'=>'Not Found Any Coupon Discount!']);
        }
    }

    public function storeSession(Request $request){

        session()->put('delivery_id', $request->delivery_id);
        session()->put('shipping_id', $request->shipping_id);

        return 'ok';

    }

}
