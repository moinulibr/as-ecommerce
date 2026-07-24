<?php
use App\Models\Discount;


function getProductDiscount($product)
{
    $now = date('Y-m-d');

    $discount = Discount::where('status', 1)
        ->whereDate('start', '<=', $now)
        ->whereDate('end', '>=', $now)
        ->where(function ($q) use ($product) {

            // Brand OR Category discount
            $q->where('brand_id', $product->brand_id)
              ->orWhere('category_id', $product->category_id);

            // Specific product discount
            $q->orWhereHas('discount_prodcuts', function ($sub_q) use ($product) {
                $sub_q->where('product_id', $product->id);
            });

        })
        ->orderBy('priority')
        ->first();

    $discount_price = 0;
    
    if ($discount) {
        if ($discount->discount_type == 'percentage') {
            $discount_price = (($product->sell_price??$product->price) * $discount->amount) / 100;
        } else { // fixed
            $discount_price = $discount->amount;
        }
    }
    

    return [
        'discount_price' => $discount_price,
        'discount'       => $discount
    ];
}


function getInfo($key){

    $item=\Cache::get('info');

    return $item[$key]??'';
}

function dateFormate($date=null){
	$value='';
	if ($date) {
		$value = date('d F Y', strtotime($date));
	}
	return $value;
}

function getImage($folder=null,$value=null){

	$url = asset('images/no_found.png');
	$path = public_path($folder.'/'.$value);
	if (!empty($folder) && (!empty($value))) {
		if(file_exists($path)){
			$url = asset($folder.'/'.$value);
		}
	}
	return $url;
}

function deleteImage($folder=null, $file=null){

    if (!empty($folder) && !empty($file)) {
        $path = public_path($folder.'/'.$file);
        $isExists = file_exists($path);
        if ($isExists) {
            unlink($path);
        }
    }
    return true;
}


function priceFormate($amount=0){
    
    return 'TK '.number_format($amount,0);
    
}

function getRole(){

	return auth()->user()->roles->pluck('name')[0] ??'';
}

function getTotalAmount($user_id=null){
    $cart = session()->get('cart', []);
    $total = 0;
    foreach($cart as $cartItem){
        if ($user_id) {
            if($cartItem['user_id'] !=$user_id){
                continue;
            }
            
        }
        $total += $cartItem['price'] * $cartItem['quantity'];
        
    }
    return $total;
}

function getCartDiscount($user_id=null){
    
    $cart = session()->get('cart', []);
    $total = 0;
    foreach($cart as $cartItem){
        if ($user_id) {
            if($cartItem['user_id'] !=$user_id){
                continue;
            }
            
        }
        $total += $cartItem['discount'] * $cartItem['quantity'];
        
    }
    return $total;
}


function totalVendorCart(){
    $carts = session()->get('cart', []);
    
    $vcarts = array_reduce($carts, function($carry, $item) {
                $carry[$item['user_id']][] = $item;
                return $carry;
            }, []);

    return count($vcarts);
}


// function getTotalCart(){

// 	return count(session()->get('cart',[]));
// }

function getTotalCart(){
    $cart = session()->get('cart', []);
    $total = 0;
    foreach ($cart as $item) {
        $total += isset($item['quantity']) ? intval($item['quantity']) : 0;
    }
    return $total;
}

function getProductInfo($product){


	$price=$product->sell_price;
	$discount_amount=$product->dicount_amount;
	
// 	$old_price=($product->after_discount > 0) ? $product->sell_price : $product->regular_price;
	$old_price=$product->sell_price;

	return ['price'=>$price,'discount_amount'=>$discount_amount,'old_price'=>$old_price];
}

function getSectionLists(){

	return ['0'=>'None','1'=>'Trending','2'=>'Hot Deals','3'=>'Recommended','4'=>'Top Brand'];

}

function getOrderStatus($type=""){

	if($type != "")
    {
     return [''=> 'All', 'pending'=>'Pending','processing'=>'Processing','courier'=>'Courier','courier_complete' => 'Courier Complete','on_hold'=>'On Hold','complete'=>'Complete','cancell'=>'Cancelled','return' => 'Return']; 
    }
  
  	return ['pending'=>'Pending','processing'=>'Processing','courier'=>'Courier','courier_complete' => 'Courier Complete','on_hold'=>'On Hold','complete'=>'Complete','cancell'=>'Cancelled','return' => 'Return'];

}

function getOrderMethod(){
	return ['cash'=>'Cash','Card'=>'Card'];
}

function SendSms($number=null,$message=null){
    
  	$data =[
            'user' => 'sahaalfash',
            'pwd' => '66pueu99',
            'senderid' => '8809617611152', 
            'CountryCode' => '+880',
            'mobileno' => $number,    
            'msgtext' => $message
    ];
  	$query = http_build_query($data);
    $url = "http://mshastra.com/sendurl.aspx?$query";
    $ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //curl_setopt($ch, CURLOPT_URL, $url);
	$curl_scraped_page =  curl_exec($ch);
	curl_close($ch);
    return $curl_scraped_page;
}

// function SendSms($number=null,$message=null){
//   	$data =[
//             'apikey' => 'fa1417caaf958cbc',
//             'secretkey' => '71fe721b',
//             'callerID' => '1234',
//             'toUser' => $number,
//             'messageContent' => $message
//     ];
//     $query = http_build_query($data);
//     $url = "http://217.172.190.215/sendtext?$query";
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_URL, $url);
//     $done =  curl_exec($ch);
    
    
//     curl_close($ch);
//     return $done;
    
// }





function getPageName(){

	return ['about'=>'About Us','return-policy'=>'Return Policy','privacy-policy'=>'Privacy Policy','term'=>'Term And Condition'];

}


function getCouponDiscount(){
    $coupon=session()->get('coupon_discount');
  	$type=session()->get('discount_type');
  
  	
    
    $cart = session()->get('cart');
    $total=0;
    $amount=0;
    
    if($cart){
        foreach($cart as $id=>$item){
            $total +=$item['price'] * $item['quantity'];
        }
    }
    
    if($coupon){
        if($type=='fixed'){
            $amount=$coupon;
        }else{
            $amount=(($total*$coupon)/ 100);
        }
        
        if(($total >0) and ($coupon)){
            $amount=$amount;
        }
    }
  	
    
	return round($amount);
}

function full_name($user)
{
    if($user)
    {
        return $user->first_name.' '.$user->last_name;
    }
    
    return '';
}

function BanglaText($index)
{      
  $bangla_text = array(
    "cust_info"             =>"কাস্টমার ইনফরমেশন",
    "offer"                 => "মেগা অফার",
    'tk'                    => "টাকা",
    "do_order"              => "অর্ডার করতে ক্লিক করুন",
    "instruction"           =>"অর্ডার কনফার্ম করতে আপনার নাম, ঠিকানা, মোবাইল নাম্বার লিখে অর্ডার কনফার্ম করুন বাটনে ক্লিক করুন",
    "name"                  => "আপনার নাম",
    "placeholder_name"      => "আপনার নাম লিখুন",
    "mobile"                => "আপনার মোবাইল নাম্বার",
    "placeholder_mobile"    => "আপনার  মোবাইল নাম্বার লিখুন",
    "address"               => "আপনার সম্পূর্ন ঠিকানা",
    "placeholder_address"   => "",
    "delivery_zone"         => "ডেলিভারি এলাকা নির্বাচন করুন",
    "confirm_order"         => "অর্ডার কনফার্ম করুন",
    "alert"                 => "* ১০০% শিউর হয়ে অর্ডার করুন, অহেতুক অর্ডার করবেন না ।",
    "order_information"     => "অর্ডার ইনফরমেশন",
    "order"                 => "অর্ডার করুন",
    "land_order"            => "অর্ডার করতে চাই",
    "cart"                  => "কার্টে যোগ করুন",
    "land_instruction"      => "অর্ডার করতে নিচের ফর্মটি সঠিক তথ্য দিয়ে পূরন করুন",
    "order_ensure"          => "১০০% শিউর হয়ে অর্ডার করুন" 
    );
  return $bangla_text[$index]; 
}


function login_url(){
    
    return route('login', ['redirect' => url()->current()]);
}


