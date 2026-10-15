🗂️ PART 1: FILE MAP (কোন file কী কাজ করে)
File Path	Layer	Purpose
app/helpers.php	Helper	cartLookup(), markCartStatus(), getTotalCart(), getTotalAmount()
app/Http/Controllers/HomeController.php	Controller	Home page render + flag
app/Http/Controllers/ProductController.php	Controller	List/Detail/Promotions/Vendor pages
app/Http/Controllers/ShopController.php	Controller	Vendor shop page
app/Http/Controllers/CartController.php	Controller	Cart CRUD (store/edit/destroy/clearAll)
app/Utils/ProductUtil.php	Utility	Stock check
resources/views/layouts/app.blade.php	Layout	window.cartState inject + cart modal wrapper
resources/views/partials/js.blade.php	Layout JS	Global JS — refreshProductCartButtons(), applyCartStateFromResponse()
resources/views/partials/header.blade.php	Partial	Cart icon, count badge
resources/views/products/section.blade.php	Partial	Product card (list/home/detail-related)
resources/views/products/index_data.blade.php	Partial	AJAX loop for product list
resources/views/products/show.blade.php	View	Product detail page
resources/views/products/index.blade.php	View	Products list page (AJAX loader)
resources/views/home.blade.php	View	Home page
resources/views/carts/cart_section.blade.php	Partial	Cart drawer content (reloaded via AJAX)
resources/views/carts/index.blade.php	View	Cart full page
public/assets/js/cart.js	JS	All cart AJAX handlers
🗺️ PART 2: ROUTE MAP
Route Name	URL	Method	Controller@Method
front.home	/	GET	HomeController@home
front.products.index	/products-list	GET	ProductController@index
front.products.show	/product-show/{slug}	GET	ProductController@show
front.getVariation	/get-variation/{id}	GET	ProductController@getVariation
front.carts.store	/carts	POST	CartController@store
front.carts.edit	/carts/{id}	GET	CartController@edit
front.carts.destroy	/carts/{id}	DELETE	CartController@destroy
front.clearAll	/carts/clear-all	GET	CartController@clearAll
front.checkouts.index	/checkouts	GET	CheckoutController@index
front.shop	/shop/{slug}	GET	ShopController@index
front.category.sliders	/category-sliders	GET	ProductController@getSliders

🎬 PART 3: USER ACTION → COMPLETE FLOW
🔵 ACTION 1: "Add to Cart" click (Product Card — Home/List/Vendor)
📍 কোথায় click?
resources/views/products/section.blade.php এর <button class="product-cart-btn" name="action" value="cart" type="submit">

🧭 Flow — Step by Step:
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — HTML FORM                                                │
├──────────────────────────────────────────────────────────────────┤
│ <form action="{{ route('front.carts.store') }}"                  │
│       method="post"                                               │
│       id="cart_form" class="cart-form">                          │
│   @csrf                                                           │
│   <input type="hidden" name="product_id"   value="5">            │
│   <input type="hidden" name="variation_id" value="12">           │
│   <input type="hidden" name="quantity"     value="1">            │
│   <button name="action" value="cart"                             │
│           class="product-cart-btn"                               │
│           data-product-id="5"                                    │
│           data-variation-id="12">                                │
│     Add to Cart                                                   │
│   </button>                                                       │
│ </form>                                                           │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — JS HANDLER (public/assets/js/cart.js)                    │
├──────────────────────────────────────────────────────────────────┤
│ $(document).on('submit', 'form#cart_form', function(e) {         │
│     e.preventDefault();              // ← page reload বন্ধ        │
│                                                                   │
│     let clickedButton = $(document.activeElement).val();          │
│     let data = $(this).serializeArray();                          │
│     data.push({ name: "button", value: clickedButton });          │
│                                                                   │
│     $.ajax({                                                      │
│         url:    $(this).attr('action'),  // /carts               │
│         method: $(this).attr('method'),  // POST                 │
│         data:   data,                                             │
│         success: function(res) { ... }                            │
│     });                                                           │
│ });                                                               │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — ROUTE MATCH                                              │
├──────────────────────────────────────────────────────────────────┤
│ POST /carts → CartController@store                                │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — CONTROLLER (app/Http/Controllers/CartController.php)    │
├──────────────────────────────────────────────────────────────────┤
│ public function store(Request $request) {                         │
│     // (a) Validate                                               │
│     $data = $request->validate([                                  │
│         'product_id'   => 'required|numeric',                     │
│         'variation_id' => 'required|numeric',                     │
│     ]);                                                           │
│                                                                   │
│     // (b) Buy Now হলে checkout URL                               │
│     if ($request->action == 'buy') {                              │
│         $url = route('front.checkouts.index');                    │
│     }                                                             │
│                                                                   │
│     // (c) Product + Variation load                               │
│     $product   = Product::findOrFail($request->product_id);       │
│     $variation = Variation::find($request->variation_id);         │
│                                                                   │
│     // (d) Discount calculate                                     │
│     $p_price        = getProductDiscount($product);  // helper    │
│     $discount_price = $p_price['discount_price'];                 │
│     $discount       = $p_price['discount'];                       │
│                                                                   │
│     // (e) Stock check                                            │
│     $stock    = $this->productUtil->checkProductStock(...);       │
│     if ($is_stock == 1 && $stock < $quantity) {                   │
│         return response()->json(['success' => false,              │
│                                  'msg' => 'Stock Not Available!']);│
│     }                                                             │
│                                                                   │
│     // (f) Session cart update                                    │
│     $cart = session()->get('cart', []);                           │
│     $cart[$variation_id] = [ ... product info ... ];              │
│     session()->put('cart', $cart);                                │
│                                                                   │
│     // (g) Response — ⭐ CRITICAL DATA ⭐                          │
│     return response()->json([                                     │
│         'success' => true,                                        │
│         'msg'     => 'Product added to cart successfully!',       │
│         'item'    => getTotalCart(),                              │
│         'html'    => view('carts.cart_section')->render(),        │
│         'url'     => $url,                                        │
│         'cart_variation_ids' => array_keys($cart),                │
│         'cart_product_ids'   => collect($cart)                    │
│                                  ->pluck('product_id')            │
│                                  ->unique()->values()->all(),     │
│     ]);                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 5 — JS SUCCESS HANDLER (cart.js)                             │
├──────────────────────────────────────────────────────────────────┤
│ success: function(res) {                                          │
│     if (res.success) {                                            │
│         toastr.success(res.msg);                                  │
│         applyCartResponseUpdates(res);   // ← কেন্দ্রীয় handler  │
│         if (res.url) window.location.href = res.url;              │
│         checkCheckout();                                          │
│     }                                                             │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 6 — applyCartResponseUpdates(res) — cart.js                  │
├──────────────────────────────────────────────────────────────────┤
│ function applyCartResponseUpdates(res) {                          │
│     // (a) Button sync (partials/js.blade.php থেকে)               │
│     applyCartStateFromResponse(res);                              │
│                                                                   │
│     // (b) Cart drawer HTML replace                               │
│     $('div#cartModal').html(res.html);                            │
│                                                                   │
│     // (c) Cart badge count update                                │
│     $(document).find('span.cart-count').text(res.item);           │
│                                                                   │
│     // (d) Cart page total update (থাকলে)                          │
│     // extract #cartTotal → #pageTotal                            │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 7 — applyCartStateFromResponse(res) — partials/js.blade.php  │
├──────────────────────────────────────────────────────────────────┤
│ function applyCartStateFromResponse(res) {                        │
│     window.cartState = {                                          │
│         variationIds: res.cart_variation_ids.map(String),         │
│         productIds:   res.cart_product_ids.map(String),           │
│     };                                                            │
│     refreshProductCartButtons();   // ← সব button re-render       │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 8 — refreshProductCartButtons() — partials/js.blade.php      │
├──────────────────────────────────────────────────────────────────┤
│ $('.product-cart-btn').each(function() {                          │
│     var pId = $(this).attr('data-product-id');                    │
│     var vId = $(this).attr('data-variation-id');                  │
│     var inCart = window.cartState.productIds.includes(pId) ||     │
│                    window.cartState.variationIds.includes(vId);   │
│                                                                   │
│     if (inCart) {                                                 │
│         $(this).addClass('bg-green-600 text-white ...')           │
│                .prop('disabled', true)                            │
│                .text('Already in cart');                          │
│     } else {                                                      │
│         $(this).addClass('bg-gray-900 text-white')                │
│                .prop('disabled', false)                           │
│                .text('Add to Cart');                              │
│     }                                                             │
│ });                                                               │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ ✅ FINAL UI                                                       │
├──────────────────────────────────────────────────────────────────┤
│ • যে product click করা হয়েছে → green + "Already in cart"          │
│ • বাকি সব button unchanged (dark + "Add to Cart")                │
│ • Cart drawer-এ নতুন item যোগ হয়েছে                              │
│ • Header-এ cart badge count বেড়েছে                               │
│ • Page reload হয়নি ✅                                             │
└──────────────────────────────────────────────────────────────────┘

🟢 ACTION 2: Cart Drawer থেকে Trash Icon Click (Remove Item)
📍 কোথায় click?
resources/views/carts/cart_section.blade.php এর <button type="submit"><i class="fas fa-trash"></i></button> — যা form.cart_remove_form এর ভিতরে

🧭 Flow:
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — HTML FORM (cart_section.blade.php)                       │
├──────────────────────────────────────────────────────────────────┤
│ <form action="{{ route('front.carts.destroy', [$key]) }}"        │
│       class="cart_remove_form"                                    │
│       data-key="{{ $key }}"                                       │
│       method="post">                                              │
│     @method('DELETE')                                             │
│     @csrf                                                         │
│     <button type="submit"><i class="fas fa-trash"></i></button>  │
│ </form>                                                           │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — JS HANDLER (cart.js)                                     │
├──────────────────────────────────────────────────────────────────┤
│ $(document).on('submit',                                         │
│     'form#cart_remove_form, form.cart_remove_form',              │
│     function(e) {                                                 │
│         e.preventDefault();                                       │
│         var ele = $(this);                                        │
│                                                                   │
│         swal({                                                    │
│             title: "Are you sure?",                               │
│             ...                                                    │
│         }, function(isConfirm) {                                  │
│             if (isConfirm) {                                      │
│                 let url    = ele.attr('action');                  │
│                 let method = ele.attr('method');                  │
│                 let data   = ele.serialize();                     │
│                                                                   │
│                 $.ajax({ type: method, url: url, data: data,      │
│                     success: function(res) {                      │
│                         if (res.success) {                        │
│                             toastr.success(res.msg);              │
│                             applyCartResponseUpdates(res);        │
│                             checkCheckout();                      │
│                         }                                         │
│                     }                                             │
│                 });                                               │
│                 swal.close();                                     │
│             }                                                     │
│         });                                                       │
│ });                                                               │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — ROUTE MATCH                                              │
├──────────────────────────────────────────────────────────────────┤
│ DELETE /carts/{id} → CartController@destroy($id)                  │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — CONTROLLER                                               │
├──────────────────────────────────────────────────────────────────┤
│ public function destroy($id) {                                    │
│     $cart = session()->get('cart', []);                           │
│     if (isset($cart[$id])) {                                      │
│         unset($cart[$id]);                                        │
│         session()->put('cart', $cart);                            │
│     }                                                             │
│                                                                   │
│     return response()->json([                                     │
│         'success' => true,                                        │
│         'msg'     => 'Product removed successfully!',             │
│         'html'    => view('carts.cart_section')->render(),        │
│         'item'    => getTotalCart(),                              │
│         'cart_variation_ids' => array_keys($cart),                │
│         'cart_product_ids'   => collect($cart)                    │
│                                  ->pluck('product_id')            │
│                                  ->unique()->values()->all(),     │
│     ]);                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 5 — JS SUCCESS (same as ACTION 1)                            │
├──────────────────────────────────────────────────────────────────┤
│ applyCartResponseUpdates(res)                                     │
│   ├── applyCartStateFromResponse(res)                             │
│   │     window.cartState update (item সরানো)                      │
│   │     refreshProductCartButtons()                               │
│   │         → product card-এর button "Add to Cart" এ ফিরে যায়    │
│   └── #cartModal HTML replace (item আর নেই)                       │
└──────────────────────────────────────────────────────────────────┘


🟡 ACTION 3: Cart Drawer-এ Quantity +/- Click
📍 কোথায় click?
cart_section.blade.php এর <button class="qtybtn quantity-plus">+</button> বা <button class="qtybtn">-</button>

🧭 Flow:
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — HTML                                                     │
├──────────────────────────────────────────────────────────────────┤
│ <div class="flex items-center" data-href="/carts/12">             │
│   <button class="qtybtn">-</button>                               │
│   <input id="quantity-12" value="2" data-max="50">                │
│   <button class="qtybtn quantity-plus">+</button>                 │
│ </div>                                                            │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — JS HANDLER (cart.js)                                     │
├──────────────────────────────────────────────────────────────────┤
│ $(document).on('click', 'button.qtybtn', function() {            │
│     var $button = $(this);                                        │
│     var $input  = $button.parent().find('input');                 │
│     var oldValue = parseFloat($input.val()) || 1;                 │
│                                                                   │
│     if ($button.hasClass('quantity-plus')) {                      │
│         var newVal = oldValue + 1;                                │
│     } else {                                                      │
│         var newVal = oldValue > 1 ? oldValue - 1 : 1;             │
│     }                                                             │
│                                                                   │
│     $input.val(newVal);                                           │
│     updateCart($input);   // ← AJAX call                          │
│ });                                                               │
│                                                                   │
│ function updateCart($input) {                                     │
│     var $button = $input.parent();                                │
│     let url = $button.closest('div').attr('data-href');           │
│                                                                   │
│     $.ajax({                                                      │
│         url: url,                 // /carts/12                    │
│         method: "GET",                                            │
│         data: {quantity: newVal, segment: segment},               │
│         success: function(res) {                                  │
│             if (res.success) {                                    │
│                 toastr.success(res.msg);                          │
│                 applyCartResponseUpdates(res);                    │
│             }                                                     │
│         }                                                         │
│     });                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — ROUTE MATCH                                              │
├──────────────────────────────────────────────────────────────────┤
│ GET /carts/{id}?quantity=N → CartController@edit($id)            │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — CONTROLLER                                               │
├──────────────────────────────────────────────────────────────────┤
│ public function edit(Request $request, $id) {                     │
│     $cart = session()->get('cart', []);                           │
│     if (empty($cart) || !isset($cart[$id])) {                     │
│         return response()->json(['success'=>false,                │
│                                   'msg'=>'Item not found']);      │
│     }                                                             │
│                                                                   │
│     if ($request->quantity == 0) {                                │
│         unset($cart[$id]);                                        │
│     } else {                                                      │
│         // stock check                                            │
│         $cart[$id]["quantity"] = $request->quantity;              │
│     }                                                             │
│                                                                   │
│     session()->put('cart', $cart);                                │
│                                                                   │
│     return response()->json([                                     │
│         'success' => true,                                        │
│         'msg'     => 'Update cart successfully!',                 │
│         'html'    => view('carts.cart_section')->render(),        │
│         'item'    => getTotalCart(),                              │
│         'cart_variation_ids' => array_keys($cart),                │
│         'cart_product_ids'   => ...,                              │
│     ]);                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘

🔵 ACTION 4: Cart Page থেকে "Clear All" Click
📍 কোথায় click?
carts/index.blade.php এর <a href="{{ route('front.clearAll') }}" class="cart_remove_form">

🧭 Flow:
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — HTML                                                     │
├──────────────────────────────────────────────────────────────────┤
│ <a href="{{ route('front.clearAll') }}"                           │
│    class="cart_remove_form">Delete All</a>                        │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — JS HANDLER (cart.js)                                     │
├──────────────────────────────────────────────────────────────────┤
│ $(document).on('click', 'a.cart_remove_form', function(e) {      │
│     e.preventDefault();                                           │
│     var ele = $(this);                                            │
│     swal({ ... }, function(isConfirm) {                           │
│         if (isConfirm) {                                          │
│             let url = ele.attr('href');    // /carts/clear-all   │
│             $.ajax({                                              │
│                 type: 'GET', url: url, data: {},                  │
│                 success: function(res) {                          │
│                     if (res.url) {                                │
│                         document.location.href = res.url;         │
│                     } else {                                      │
│                         applyCartResponseUpdates(res);            │
│                     }                                             │
│                 }                                                 │
│             });                                                   │
│         }                                                         │
│     });                                                           │
│ });                                                               │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — ROUTE MATCH                                              │
├──────────────────────────────────────────────────────────────────┤
│ GET /carts/clear-all → CartController@clearAll()                  │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — CONTROLLER                                               │
├──────────────────────────────────────────────────────────────────┤
│ public function clearAll(Request $request) {                      │
│     if (isset($request->id)) {                                    │
│         // single item remove                                     │
│     } else {                                                      │
│         // সব clear + coupon reset                                │
│         session()->put('cart', []);                               │
│         session()->put('coupon_discount', null);                  │
│     }                                                             │
│                                                                   │
│     $url = '';                                                    │
│     if (isset($request->url)) {                                   │
│         $url = route('front.carts.index');                        │
│     } elseif (getTotalCart() == 0) {                              │
│         $url = route('front.home');                               │
│     }                                                             │
│                                                                   │
│     return response()->json([                                     │
│         'success' => true,                                        │
│         'url'     => $url,          // ← redirect URL             │
│         'html'    => view('carts.cart_section')->render(),        │
│         'item'    => getTotalCart(),                              │
│         'cart_variation_ids' => [],                               │
│         'cart_product_ids'   => [],                               │
│     ]);                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 5 — JS SUCCESS                                               │
├──────────────────────────────────────────────────────────────────┤
│ if (res.url) {                                                    │
│     window.location.href = res.url;  // ← page redirect           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘

🟣 ACTION 5: Product Detail Page — Variation Select
📍 কোথায় click?
products/show.blade.php এর <button class="colors"> বা <button class="sizes">

🧭 Flow:
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — HTML                                                     │
├──────────────────────────────────────────────────────────────────┤
│ <button class="colors" style="background-color: red">Red</button> │
│ <button class="sizes">XL</button>                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — JS HANDLER (show.blade.php)                              │
├──────────────────────────────────────────────────────────────────┤
│ $('.sizes, .colors').on('click', function() {                     │
│     $('.sizes, .colors').removeClass('active');                   │
│     $(this).addClass('active');                                   │
│                                                                   │
│     setTimeout(function() {                                       │
│         getVariation();       // ← AJAX call                      │
│     }, 100);                                                      │
│ });                                                               │
│                                                                   │
│ function getVariation() {                                         │
│     let url   = '{{ route("front.getVariation", [$product->id]) }}';│
│     let color = $('.colors.active').text() || null;               │
│     let size  = $('.sizes.active').text()  || null;               │
│                                                                   │
│     $.ajax({                                                      │
│         url: url,                                                 │
│         data: {size, color},                                      │
│         success: function(res) {                                  │
│             if (res.variation) {                                  │
│                 $('#variation_id').val(res.variation.id);         │
│                 $('.product_price').text(res.variation.sell_price);│
│                                                                   │
│                 // Button label update                            │
│                 let vId = String(res.variation.id);               │
│                 let vIds = window.cartState.variationIds;         │
│                 let $btn = $('#cart_form .product-cart-btn');     │
│                                                                   │
│                 if (vIds.includes(vId)) {                         │
│                     $btn.find('.cart-btn-label')                  │
│                         .text('Update Cart');                     │
│                 } else {                                          │
│                     $btn.find('.cart-btn-label')                  │
│                         .text('Add to Cart');                     │
│                 }                                                 │
│             }                                                     │
│             checkVariation();                                     │
│         }                                                         │
│     });                                                           │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — ROUTE MATCH                                              │
├──────────────────────────────────────────────────────────────────┤
│ GET /get-variation/{id}?size=XL&color=Red → ProductController@getVariation│
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — CONTROLLER                                               │
├──────────────────────────────────────────────────────────────────┤
│ public function getVariation($id) {                               │
│     $name = '';                                                   │
│     $size  = request('size');                                     │
│     $color = request('color');                                    │
│                                                                   │
│     if ($color) $name .= $color;                                  │
│     if ($size)  $name .= ($name ? '-' : '') . $size;              │
│                                                                   │
│     $variation = Variation::where('product_id', $id)              │
│                              ->where('name', $name)->first();     │
│                                                                   │
│     return response()->json(['variation' => $variation]);         │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘

🟠 ACTION 6: Page Load — Initial Button State
🧭 Flow (No user action):
┌──────────────────────────────────────────────────────────────────┐
│ STEP 1 — USER OPENS PAGE                                          │
├──────────────────────────────────────────────────────────────────┤
│ GET / → HomeController@home()                                     │
│ GET /products-list → ProductController@index()                    │
│ GET /product-show/{slug} → ProductController@show()               │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 2 — CONTROLLER (e.g., HomeController)                        │
├──────────────────────────────────────────────────────────────────┤
│ $products = Product::with('variation')->...->get();               │
│ $followedProducts = Product::...->get();                          │
│                                                                   │
│ markCartStatus($products);            // ← flag set               │
│ markCartStatus($followedProducts);    // ← flag set               │
│                                                                   │
│ return view('home', compact('products', ...));                    │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 3 — HELPER markCartStatus() (app/helpers.php)                │
├──────────────────────────────────────────────────────────────────┤
│ function markCartStatus($products) {                              │
│     $lookup = cartLookup();       // session cart পড়ে             │
│                                                                   │
│     foreach ($products as $p) {                                   │
│         $pid = (string) $p->id;                                   │
│         $p->isAddedToCart = isset($lookup['products'][$pid]);     │
│     }                                                             │
│ }                                                                 │
│                                                                   │
│ function cartLookup() {                                           │
│     static $cache = null;                                         │
│     if ($cache) return $cache;                                    │
│                                                                   │
│     $cart = session()->get('cart', []);                           │
│     $vIds = []; $pIds = [];                                       │
│                                                                   │
│     foreach ($cart as $vid => $item) {                            │
│         $vIds[(string)$vid] = true;                               │
│         if (!empty($item['product_id']))                          │
│             $pIds[(string)$item['product_id']] = true;            │
│     }                                                             │
│     return $cache = ['variations'=>$vIds, 'products'=>$pIds];     │
│ }                                                                 │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 4 — BLADE RENDER (products/section.blade.php)                │
├──────────────────────────────────────────────────────────────────┤
│ @php $inCart = !empty($sproduct->isAddedToCart); @endphp          │
│                                                                   │
│ <button class="product-cart-btn                                    │
│     {{ $inCart ? 'bg-green-600 text-white'                        │
│                  : 'bg-gray-900 text-white' }}"                   │
│     data-product-id="{{ $sproduct->id }}"                         │
│     data-variation-id="{{ $sproduct->variation->id }}">           │
│                                                                   │
│     {{ $inCart ? 'Already in cart' : 'Add to Cart' }}             │
│ </button>                                                         │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 5 — LAYOUT (layouts/app.blade.php)                           │
├──────────────────────────────────────────────────────────────────┤
│ @php $__cartState = cartLookup(); @endphp                         │
│                                                                   │
│ <script>                                                          │
│ window.cartState = {                                              │
│     variationIds: @json(array_keys($__cartState['variations'])),  │
│     productIds:   @json(array_keys($__cartState['products']))     │
│ };                                                                │
│ </script>                                                         │
└──────────────────────────────────────────────────────────────────┘
                              ↓
┌──────────────────────────────────────────────────────────────────┐
│ STEP 6 — JS READY (partials/js.blade.php)                         │
├──────────────────────────────────────────────────────────────────┤
│ $(document).ready(function() {                                    │
│     refreshProductCartButtons();  // final sync                   │
│ });                                                               │
└──────────────────────────────────────────────────────────────────┘
🔧 PART 4: HELPER FUNCTIONS REFERENCE
cartLookup() — app/helpers.php
Input: নেই
Output:
[
  'variations' => ['12'=>true, '15'=>true],   // cart keys
  'products'   => ['5'=>true, '8'=>true],     // product ids
]
Use: session cart থেকে fast lookup map

markCartStatus($products)
Input: Collection/Paginator/Array of Products
Effect: প্রতিটা $p->isAddedToCart set করে (bool)
No DB query — শুধু in-memory lookup

getTotalCart()
Output: total quantity (সব item-এর sum)

getTotalAmount($user_id=null)
Output: total price (quantity × price)

🎨 PART 5: DATA ATTRIBUTES REFERENCE
Product Card Button (products/section.blade.php)
<button class="product-cart-btn"
        data-product-id="5"
        data-variation-id="12"
        data-add-text="Add to Cart"
        data-in-cart-text="Already in cart">

JS কীভাবে use করে:
var pId = $btn.attr('data-product-id');     // "5"
var vId = $btn.attr('data-variation-id');   // "12"
// window.cartState.productIds.includes(pId) বা
// window.cartState.variationIds.includes(vId)

Product Detail Button (products/show.blade.php)
<button class="product-cart-btn"
        data-product-id="5"
        data-variation-id="12"
        data-add-text="Add to Cart"
        data-in-cart-text="Update Cart">
    <div class="cart-btn-label">Add to Cart</div>
</button>
Difference: .cart-btn-label wrapper আছে → detail page হিসেবে চেনে → শুধু label update, disable নয়

Cart Item (carts/cart_section.blade.php)
<div class="cart-item"
     data-product-id="5"
     data-variation-id="12"
     data-cart-key="12">

🔄 PART 6: STATE FLOW DIAGRAM
SERVER SESSION CART
   │
   │ $cart = session('cart')
   │
   ▼
cartLookup() ─── returns {variations: {...}, products: {...}}
   │
   ├──────────────────┐
   │                  │
   ▼                  ▼
markCartStatus()   window.cartState (layout inject)
   │                  │
   ▼                  │
$product->isAddedToCart  │
   │                  │
   ▼                  │
BLADE: $inCart flag      │
   │                  │
   ▼                  │
Initial Button render    │
(green/dark)             │
   │                  │
   └─────► window.cartState ◄──────┐
              │                    │
              ▼                    │
   refreshProductCartButtons()    │
              │                    │
              ▼                    │
   Final Button State              │
                                   │
                    AJAX response (cart_variation_ids,
                    cart_product_ids) ───┘


📋 PART 7: QUICK DEBUG CHECKLIST
কিছু কাজ না করলে এই order-এ check করো:

❌ Symptom	🔍 Check
সব button dark হয়ে যায় / একটাতে click করলে সবগুলো change হয়	Button-এ data-product-id / data-variation-id আছে? Form-এ আছে কি না?
Button text কিন্তু সাদা না (কালো হয়ে যায়)	refreshProductCartButtons()-এ text-white include করা আছে?
Cart drawer থেকে remove-এ page reload হয়	form.cart_remove_form handler আছে? JS-এ selector form#cart_remove_form, form.cart_remove_form?
AJAX response-এ cart_variation_ids নেই	CartController::store/edit/destroy/clearAll-এ response update করা আছে?
Page load-এ button state ভুল	markCartStatus() কল হয়েছে controller-এ? @php $inCart = ... @endphp section-এ আছে?
window.cartState undefined	layouts/app.blade.php-এ inject করা আছে? @json(...) output?
Detail page-এ variation select করলে button update হয় না	getVariation() success handler update করা আছে?
Cart drawer-এর HTML replace হয় কিন্তু badge update হয় না	applyCartResponseUpdates()-এ span.cart-count update আছে?


🎯 PART 8: FUTURE CHANGE CHEAT SHEET
কী change করতে চাও	কোথায় edit করবে
Button-এর color	products/section.blade.php (server) + partials/js.blade.php (CART_BTN_DARK_CLASSES, CART_BTN_GREEN_CLASSES)
Button-এর text	products/section.blade.php (initial) + translation file + data-add-text/data-in-cart-text
New action যোগ করা (যেমন wishlist)	CartController-এ method + route + cart.js handler + section.blade.php
Cart drawer-এর design	carts/cart_section.blade.php only
AJAX response-এ নতুন field	CartController::store/edit/destroy/clearAll + applyCartResponseUpdates()
Real-time refresh-এ নতুন element	refreshProductCartButtons() (partials/js.blade.php)


🏁 PART 9: KEY PRINCIPLES (মনে রাখো)
Single Source of Truth: Cart state দুই জায়গায় থাকে — server session এবং client window.cartState। AJAX response-এ server → client sync করে।

Zero Reload: সব cart action AJAX — কোনো page reload নেই (Buy Now এবং Clear All redirect বাদে)।

Centralized Handler: সব cart AJAX success applyCartResponseUpdates(res) কল করে — এক জায়গায় change করলেই সব flow update।

Server-rendered Initial State + Client-rendered Realtime: Page load-এ Blade $inCart flag দিয়ে button render। AJAX-এ JS window.cartState দিয়ে update।

Data Attributes on Button (Not Form): data-product-id / data-variation-id button-এ থাকতে হবে — JS ওখান থেকে পড়ে।

N+1 Free: markCartStatus() কোনো DB query করে না — শুধু session cart in-memory lookup।

Session-based, Not DB: পুরো cart system session-এ চলে — DB-তে cart table নেই।
