<div class="bg-gray-50 p-6 rounded-lg border border-gray-100">
    <h2 class="text-xl font-bold mb-4 text-black">Cart Totals</h2>
    
    @foreach($cart as $ikey => $i)
        <div class="border border-gray-200 p-4 rounded-md bg-white {{ count($cart) > 1 && !$loop->last ? 'mb-4' : '' }}">
            <div class="flex flex-col sm:flex-row gap-4">
                <div class="w-24 h-24 flex-shrink-0">
                    <img src="{{$i['image']}}" alt="{{ $i['name'] }}" class="w-full rounded-md object-contain h-[100px]">
                </div>
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:justify-between">
                        <div class="font-medium text-gray-900 text-[15px]">{{$i['name']}}</div>
                        <p class="text-orange-500 font-medium whitespace-nowrap">
                            @if($i['discount'] > 0)
                                <span class="line-through text-gray-400 text-xs mr-1">{{ priceFormate($i['old_price']) }}</span>
                            @endif
                            {{ priceFormate($i['price']) }} &times; {{ $i['quantity'] }} = {{ priceFormate($i['quantity'] * $i['price']) }}
                        </p>
                    </div>
                    
                    @if(!empty($i['variation_name']) && $i['variation_name'] != 'dummy')
                        <p class="text-gray-600 text-sm mt-1">{{$i['variation_name']}}</p>
                    @endif

                    <div class="flex justify-end mt-2">
                        <div class="flex items-center space-x-2">
                            <div class="text-sm text-gray-600">Quantity:</div>
                            <div class="flex items-center" data-href="{{ route('front.carts.edit',[$ikey])}}">
                                <button type="button" class="border border-gray-300 px-3 py-1 rounded-l qtybtn">-</button>
                                <input type="text" value="{{ $i['quantity'] }}" name="quantity" data-max="{{ $i['qty_available'] }}"
                                    class="border-t border-b border-gray-300 w-12 py-1 text-center">
                                <button type="button" class="border border-gray-300 px-3 py-1 rounded-r qtybtn quantity-plus">+</button>
                            </div>
                            
                            <a class="text-red-500 hover:text-red-700 cart_remove_form ml-2" href="{{ route('front.clearAll',['id'=>$ikey])}}">
                                <i class="fas fa-trash text-sm sm:text-base"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Shipping Options Section -->
    <div class="py-4 border-b border-gray-200">
        <h3 class="font-bold text-gray-900 text-sm mb-3">Shipping</h3>
        <div class="space-y-2 text-sm">
            @foreach($charges as $key => $charge)
            <label class="flex items-center justify-between cursor-pointer">
                <span class="flex items-center gap-2 text-gray-800">
                    <input type="radio" name="delivery_id" class="delivery_id w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500" value="{{$charge->id}}" data-price="{{$charge->amount}}" 
                    {{ $charge->id == $delivery_id ? 'checked' : ''}}>
                    {{$charge->title}} ({{$charge->description}})
                </span>
                <span class="font-medium text-gray-900">{{priceFormate($charge->amount)}}</span>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Coupon Section -->
    <div class="py-4 border-b border-gray-200">
        <label for="coupon_code" class="block font-bold text-gray-900 text-sm mb-2">Coupon Code</label>
        <div class="flex items-center gap-2">
            <input type="text" placeholder="Enter coupon code" id="coupon_code" 
                class="w-full bg-white border border-gray-300 rounded-md py-2 px-3 text-sm focus:outline-none focus:border-blue-500 transition">
            <button id="coupon_apply" type="button" data-href="{{ route('front.getCouponDiscount')}}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-medium text-sm transition-colors whitespace-nowrap">
                {{ __('messages.apply') }}
            </button>
        </div>
    </div>

    <!-- Final Total Summary -->
    <div class="space-y-2 pt-4">
        <div class="flex justify-between text-sm">
            <p class="text-gray-600">{{ __('messages.items_total') }} (<span id="checkoutItemsCount">{{getTotalCart()}}</span> {{ __('messages.items') }})</p>
            <p class="text-gray-800 font-medium">
                <span id="checkoutTotalAmount" class="sub_total" data-totalamount="{{getTotalAmount() -$cdiscount}}" data-totalvendor="{{totalVendorCart()}}">
                    {{priceFormate(getTotalAmount())}}
                </span>
            </p>
        </div>
    
        <div class="flex justify-between text-sm">
            <p class="text-gray-600">
                {{ __('messages.delivery_fee') }}
                @if(totalVendorCart() > 1)
                    ({{totalVendorCart()}} Vendors Product)
                @endif
            </p>
            <p class="text-gray-800 font-medium"><span class="charge">{{ priceFormate(0) }}</span></p>
        </div>

        @if($cdiscount)
        <div class="flex justify-between text-sm text-green-600 font-medium">
            <p>Coupon Discount</p>
            <p>- {{ priceFormate($cdiscount)}}</p>
        </div>
        @endif
        
        @if(getCartDiscount())
        <div class="flex justify-between text-sm text-green-600 font-medium">
            <p>Discount</p>
            <p>- {{ priceFormate(getCartDiscount())}}</p>
        </div>
        @endif
    
        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
            <p class="text-gray-900 font-bold text-base">{{ __('messages.total') }}:</p>
            <p class="font-bold text-lg text-gray-900">
                <span class="total_amount">{{priceFormate(getTotalAmount() - $cdiscount)}}</span>
            </p>
        </div>
    </div>

    <!-- Submit Button -->
    <button type="submit" id="btn_submit_order" class="w-full mt-6 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed text-white font-bold py-3 px-4 rounded-md shadow transition flex items-center justify-center gap-2 text-base md:text-lg">
        <svg id="btn_cart_icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>

        <svg id="btn_spinner" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <span id="btn_text">Place Order — <span class="total_amount">{{ priceFormate(getTotalAmount() - $cdiscount) }}</span></span>
    </button>
</div>