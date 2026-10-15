<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Variation;

use App\Utils\ProductUtil;
use Illuminate\Support\Facades\DB;

class CartControllerBeforeMultiVariant extends Controller
{
    public $productUtil;
    public function __construct(ProductUtil $productUtil)
    {

        $this->productUtil = $productUtil;
    }


    public function index()
    {
        return redirect()->route('front.home')->with('error', 'This section is temporarily disabled.');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id'   => 'required|numeric',
            'variation_id' => 'required|numeric',
        ]);

        $url = '';
        // ✅ FIX: form-এ name="action" — তাই $request->button নয়, $request->action
        if ($request->action && $request->action == 'buy') {
            $url = route('front.checkouts.index');
        }

        $product_id   = $request->product_id;
        $variation_id = $request->variation_id;
        $quantity     = max(1, (int) $request->quantity);

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

        if (isset($cart[$variation_id])) {

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

        // ✅ cart state for real-time button update
        cartLookup(); // reset static cache (session just changed)

        return response()->json([
            'url'     => $url,
            'html'    => $view,
            'success' => true,
            'msg'     => 'Product added to cart successfully!',
            'item'    => $total_item,
            'cart_variation_ids' => array_keys($cart),
            'cart_product_ids'   => collect($cart)->pluck('product_id')->unique()->values()->all(),
            'add_text'           => __('messages.add_to_cart') ?? 'Add to Cart',
            'already_text'       => __('messages.already_in_cart') ?? 'Already in cart',
        ]);
    }

    public function edit(Request $request, $id)
    {
        if ($id) {
            $qty = $request->quantity;

            // ✅ Guard: cart null বা empty হলে crash হবে না
            $cart = session()->get('cart', []);
            if (empty($cart) || !isset($cart[$id])) {
                return response()->json(['success' => false, 'msg' => 'Item not found in cart']);
            }

            $totalPrice = 0;
            foreach ($cart as $item) {
                $totalPrice += $item['price'] * $item['quantity'];
            }

            $segm = 'home';
            if ($request->segment) {
                $segm = $request->segment;
            }

            if ($qty == 0) {
                unset($cart[$id]);
            } else {
                $product_id = $cart[$id]["product_id"];
                $is_stock = $cart[$id]["stock_manage"];
                $stock = $this->productUtil->checkProductStock($product_id, $id, 2);
                if ($is_stock == 1 && $stock < $qty) {
                    return response()->json(['success' => false, 'msg' => ' Stock Not Available!']);
                }

                $cart[$id]["quantity"] = $qty;
            }

            session()->put('cart', $cart);
            $view = view('carts.cart_section')->render();

            return response()->json([
                'success' => true,
                'msg' => 'Update cart successfully!',
                'html' => $view,
                'item' => getTotalCart(),
                'cart_variation_ids' => array_keys($cart),
                'cart_product_ids'   => collect($cart)->pluck('product_id')->unique()->values()->all(),
                'add_text'           => __('messages.add_to_cart') ?? 'Add to Cart',
                'already_text'       => __('messages.already_in_cart') ?? 'Already in cart',
            ]);
        } else {
            return response()->json(['success' => false, 'msg' => ' Something Went Wrong !']);
        }
    }


    public function destroy($id)
    {
        if ($id) {

            // ✅ Guard
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }

            $view = view('carts.cart_section')->render();
            $total_item = getTotalCart();
            return response()->json([
                'success' => true,
                'msg' => 'Product removed successfully !',
                'html' => $view,
                'item' => $total_item,
                'cart_variation_ids' => array_keys($cart),
                'cart_product_ids'   => collect($cart)->pluck('product_id')->unique()->values()->all(),
                'add_text'           => __('messages.add_to_cart') ?? 'Add to Cart',
                'already_text'       => __('messages.already_in_cart') ?? 'Already in cart',
            ]);
        } else {
            return response()->json(['status' => false, 'msg' => ' Something Went Wrong !']);
        }
    }

    public function clearAll(Request $request)
    {
        if (isset($request->id)) {

            $id = $request->id;
            // ✅ Guard
            $cart = session()->get('cart', []);
            if (isset($cart[$id])) {
                unset($cart[$id]);
                session()->put('cart', $cart);
            }
        } else {
            session()->put('cart', []);
            session()->put('coupon_discount', []);
            session()->put('coupon_discount', null);
            session()->put('discount_type', null);
            session()->put('coupon_id', null);
            $cart = [];
        }

        $total_item = getTotalCart();
        $view = view('carts.cart_section')->render();

        $url = '';
        if (isset($request->url)) {
            $url = route('front.carts.index');
        } elseif ($total_item == 0) {
            $url = route('front.home');
        }
        return response()->json([
            'success' => true,
            'url' => $url,
            'msg' => 'Product removed successfully !',
            'html' => $view,
            'item' => $total_item,
            'cart_variation_ids' => array_keys($cart),
            'cart_product_ids'   => collect($cart)->pluck('product_id')->unique()->values()->all(),
            'add_text'           => __('messages.add_to_cart') ?? 'Add to Cart',
            'already_text'       => __('messages.already_in_cart') ?? 'Already in cart',
        ]);
    }
}
