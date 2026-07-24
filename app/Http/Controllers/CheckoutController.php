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
use App\Models\Contact;
use App\Models\Variation;
use App\Models\DeliveryCharge as Charge;
use App\Models\UserAddress;
use App\Models\Transaction;
use App\Models\TransactionLine;
use App\Models\VendorOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public $productUtil;

    public function __construct(ProductUtil $productUtil)
    {
        $this->productUtil = $productUtil;
    }

    public function index(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('front.home');
        }

        // Coupon validation check
        $coupon = session()->get('coupon_discount');
        $coupn_item = Coupon::where('amount', $coupon)->first();

        if ($coupn_item && $coupon && ($coupn_item->minimum_amount > getTotalAmount())) {
            session()->put('coupon_discount', null);
            session()->put('discount_type', null);
            session()->put('coupon_id', null);
        }

        $user_id = Auth::id();
        $charges = Charge::whereStatus(1)->whereIsNew(0)->get();
        $address = UserAddress::whereUserId($user_id)->get();
        $delivery_id = session()->get('delivery_id') ?? 3;
        $shipping_id = session()->get('shipping_id');
        $cdiscount = getCouponDiscount();

        // AJAX রিকোয়েস্টে সরাসরি ভিউ রেন্ডার রিটার্ন করবে (Raw string/JSON ছাড়াই)
        if ($request->ajax()) {
            return view('checkouts.data', compact('cart', 'charges', 'address', 'cdiscount', 'shipping_id', 'delivery_id'));
        }

        // পেজ লোডে প্রথমবার সরাসরি ডাটা পাঠাবে
        return view('checkouts.index', compact('cart', 'charges', 'address', 'cdiscount', 'shipping_id', 'delivery_id'));
    }

    public function store(Request $request)
    {
        $contact_data = $request->validate([
            'delivery_id' => 'required',
            'mobile'      => 'required|string|regex:/^(?:\+?88)?01[3-9]\d{8}$/',
            'name'        => 'required',
            'address'     => 'required',
            'email'       => 'nullable|email'
        ], [
            'delivery_id.required' => 'Please Select A Delivery Method',
        ]);

        unset($contact_data['delivery_id']);

        DB::beginTransaction();
        try {
            $contact = Contact::updateOrCreate([
                'mobile' => $contact_data['mobile']
            ], $contact_data);

            $location_id = 2;
            $cart_discount = 0;
            $cdiscount = getCouponDiscount();

            $charge = Charge::find($request->delivery_id);
            $charge_amount = $charge ? $charge->amount : 0;
            $total_charge = $charge_amount * totalVendorCart();

            $data = [
                'note'             => $request->note,
                'delivery_id'      => $request->delivery_id,
                'user_id'          => Auth::id() ?? null,
                'contact_id'       => $contact->id,
                'transaction_date' => date('Y-m-d H:i:s'),
                'invoice_no'       => rand(111111, 999999),
                'shipping_charge'  => $total_charge,
                'payment_method'   => 'cash_on_delivery',
                'discount_amount'  => $cdiscount + getCartDiscount(),
                'cal_discount'     => $cdiscount + getCartDiscount(),
                'discount_type'    => 'fixed',
                'coupon_id'        => session()->get('coupon_id'),
                'is_new'           => 0,
                'mail_notification' => 1,
                'sms_notification' => 1,
                'location_id'      => $location_id,
                'type'             => 'sell',
                'final_amount'     => getTotalAmount() + $total_charge - $cdiscount,
                'sub_total'        => getTotalAmount() + $cdiscount + getCartDiscount(),
            ];

            $sell = Transaction::create($data);
            $carts = session()->get('cart', []);

            $vcarts = array_reduce($carts, function ($carry, $item) {
                $carry[$item['user_id']][] = $item;
                return $carry;
            }, []);

            $line_data = [];
            foreach ($vcarts as $vendor_id => $vcart) {
                $vendororder = VendorOrder::create([
                    'transaction_id'  => $sell->id,
                    'vendor_id'       => $vendor_id,
                    'discount_type'   => 'fixed',
                    'shipping_charge' => $charge_amount,
                    'invoice_no'      => rand(111111, 999999),
                    'final_amount'    => getTotalAmount($vendor_id) + $charge_amount,
                    'sub_total'       => getTotalAmount($vendor_id) + getCartDiscount($vendor_id),
                    'discount_amount' => getCartDiscount($vendor_id),
                ]);

                foreach ($vcart as $cart) {
                    $variation_id = $cart['variation_id'];
                    $product_id   = $cart['product_id'];
                    $qty          = $cart['quantity'];

                    $cart_discount += $cart['discount'] * $qty;

                    $line_data[] = [
                        'product_id'      => $product_id,
                        'variation_id'    => $variation_id,
                        'vendor_order_id' => $vendororder->id,
                        'quantity'        => $qty,
                        'price'           => $cart['price'],
                        'old_price'       => $cart['old_price'],
                        'discount'        => $cart['discount'],
                        'discount_id'     => $cart['discount_id'],
                    ];

                    if (!empty($cart["stock_manage"])) {
                        $this->productUtil->decreaseProductStock($product_id, $variation_id, $location_id, $qty);
                    }
                }
            }

            if (!empty($line_data)) {
                $sell->lines()->createMany($line_data);
            }

            try {
                $this->productUtil->sendNotification($sell);
            } catch (\Throwable $e) {
                Log::error('Mail failed: ' . $e->getMessage());
            }

            session()->put('cart', []);
            session()->put('coupon_discount', null);
            session()->put('discount_type', null);
            session()->put('coupon_id', null);

            DB::commit();

            return response()->json([
                'success' => true,
                //'url'     => route('front.confirmOrder', [$sell->id]),
                'url'     => route('front.confirmOrder', [urlencode(Crypt::encryptString($sell->id))]),
                'msg'     => 'Checkout Successfully..!!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg'     => 'Something went wrong!'
            ], 500);
        }
    }

    public function sellPayment($id)
    {
        $sell = Transaction::find($id);
        return view('checkouts.payment', compact('sell'));
    }

    public function sellPaymentStore($id)
    {
        request()->validate([
            'payment_method' => 'required',
        ], [
            'payment_method.required' => 'Please Select A Payment Method',
        ]);

        $sell = Transaction::find($id);
        $sell->payment_method = request('payment_method');
        $sell->save();

        return response()->json([
            'success' => true,
            'url'     => route('front.confirmOrder', [$sell->id]),
            'msg'     => 'Payment Method Added..!!'
        ]);
    }

    public function confirmOrder($urlid)
    {
        $id = Crypt::decryptString(urldecode($urlid));
        $sell = Transaction::find($id);
        $products = Product::with('variation')
            ->where('products.is_new', 0)
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
                DB::raw('max(v.sell_price) as price'),
                DB::raw('SUM(ps.qty_available) as qty_available')
            )
            ->groupBy('products.id')
            ->take(12)
            ->get();

        return view('checkouts.thank_you', compact('sell', 'products'));
    }

    public function getCouponDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);

        try {
            $cart = session()->get('cart', []);
            $total = 0;

            if (!empty($cart)) {
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }
            }

            $today = now()->format('Y-m-d');

            $item = Coupon::where('code', $request->code)
                ->where('status', 1)
                ->where(function ($row) use ($total) {
                    $row->where('minimum_amount', '0')
                        ->orWhereNull('minimum_amount')
                        ->orWhere('minimum_amount', '<=', $total);
                })
                ->whereDate('start', '<=', $today)
                ->whereDate('end', '>=', $today)
                ->first();

            if ($item) {
                session()->put('coupon_discount', $item->amount);
                session()->put('discount_type', $item->discount_type);
                session()->put('coupon_id', $item->id);

                return response()->json([
                    'success' => true,
                    'msg'     => 'কুপন সফলভাবে প্রয়োগ করা হয়েছে!'
                ]);
            }

            return response()->json([
                'success' => false,
                'msg'     => 'অবৈধ বা মেয়াদোত্তীর্ণ কুপন কোড!'
            ]);
        } catch (\Exception $e) {
            //\Illuminate\Support\Facades\Log::error('Coupon Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'msg'     => 'কুপন প্রসেস করতে সমস্যা হচ্ছে: ' . $e->getMessage()
            ], 200); // 200 OK দিয়ে JSON রিটার্ন করছি যেন JS error block এ না যায়
        }
    }

    public function storeSession(Request $request)
    {
        session()->put('delivery_id', $request->delivery_id);
        session()->put('shipping_id', $request->shipping_id);

        return response()->json(['status' => 'ok']);
    }
}
