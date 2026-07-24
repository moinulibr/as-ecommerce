<!-- Left Column - Shipping & Product Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Shipping & Billing Section -->
            <div class="shadow-sm border rounded-lg p-6">
                <div class="flex justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-800">{{ __('messages.shipping_billing') }}</h2>
                    <!-- Add New Address Option -->
                    <div class="text-center">
                        <a href="{{ route('front.user-address.create')}}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition btn_modal">
                           {{ __('messages.add_new_address') }}
                        </a>
                    </div>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @forelse($address as $ky=>$ad)
                    <label class="border rounded-lg p-4 cursor-pointer transition-all duration-200 hover:shadow-md flex flex-col"
                           :class="{'border-blue-500 bg-blue-50': {{ $ad->id }} == {{ $shipping_id }}}">
                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="shipping_id" {{ $ad->id==$shipping_id ? 'checked':''}} 
                                       value="{{ $ad->id}}" class="shipping_id text-blue-500 focus:ring-blue-500">
                                <h3 class="text-lg font-semibold text-gray-800">{{$ad->name}}</h3>
                            </div>
                            <div class="flex space-x-2">
                                <a href="{{ route('front.user-address.edit', [$ad->id]) }}" 
                                   class="text-blue-500 hover:text-blue-700 btn_modal" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                
                                <button class="text-red-500 hover:text-red-700" title="Delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <p class="text-gray-800">{{$ad->phone}}</p>
                        <p class="text-gray-600">{{$ad->address}}</p>
                    </label>
                    @empty
                    <input type="hidden" name="shipping_id" value="" >
                    @endforelse
                
                    <div class="error_shipping_id text-red-500 mt-2"></div>
                </div>
            </div>

            <!-- Package Information -->
            <div class="bg-white p-6 shadow-sm border rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold text-gray-800">{{ __('messages.delivery_method') }}</h2>
                    
                </div>

                <!-- Delivery Option -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    
                    <!-- Delivery Option 1 -->
                    @foreach($charges as $key=> $charge)
                    <label class="border rounded-lg p-4 mb-6 cursor-pointer transition-all duration-200 hover:shadow-md has-[:checked]:border-blue-500 has-[:checked]:bg-blue-50 flex-1">
                        <div class="flex items-center mb-2">
                            <input type="radio" name="delivery_id" value="{{$charge->id}}" class="delivery_id mr-2 text-blue-500 focus:ring-blue-500" 
                            {{ $charge->id==$delivery_id ? 'checked':''}} data-price="{{$charge->amount}}">
                            <h3 class="font-bold text-gray-800">{{$charge->title}}</h3>
                        </div>
                        
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-gray-500">{{$charge->description}}</span>
                            <span class="ml-auto text-orange-500 font-medium">{{priceFormate($charge->amount)}}</span>
                        </div>
                    </label>
                    @endforeach
                    
                    
                    
                </div>
                
                <div class="error_delivery_id pb-4">
                        
                </div>

                <!-- Product Details -->
                
             
                @foreach($cart as $ikey=>$i)
                
                    <div class="border border-gray-200 p-4 rounded-md {{ count($cart) > 1 && !$loop->last ? 'mb-4' : '' }}">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="w-24 h-24 flex-shrink-0">
                                <img src="{{$i['image']}}" alt="Joyroom Shower" class="w-full rounded-md" style="height: 100px; object-fit: contain;">
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:justify-between">
                                    <h3 class="font-medium text-gray-800">{{$i['name']}}</h3>
                                    <p class="text-orange-500 font-medium whitespace-nowrap">
                                        @if($i['discount'] >0)
                                            <span class="line-through text-gray-400 text-xs"> {{$i['old_price'] }} </span>
                                        @endif
                                        
                                        {{ round($i['price'])}} X {{ $i['quantity']}} = {{ priceFormate($i['quantity'] * $i['price'])}}
                                    </p>
                                </div>
                                
                                @if($i['variation_name'] !='dummy')
                                <p class="text-gray-600 text-sm mt-1">{{$i['variation_name']}}</p>
                                @endif
                                <!-- <p class="text-red-500 text-sm mt-1">Only 144 item(s) in stock</p> -->
                                <div class="flex justify-end mt-2">
                                    <div class="flex items-center space-x-2">
                                        <div class="text-sm text-gray-600">Quantity:</div>
                                        <div class="flex items-center" data-href="{{ route('front.carts.edit',[$ikey])}}">
                                            <button type="button" class="border border-gray-300 px-3 py-1 rounded-l qtybtn">-</button>
                                            <input type="text" value="{{ $i['quantity'] }}" name="quantity"
                                                class="border-t border-b border-gray-300 w-12 py-1 text-center">
                                            <button type="button" class="border border-gray-300 px-3 py-1 rounded-r qtybtn quantity-plus">+</button>
                                        </div>
                                        
                                        
                                        <a class="text-red-500 hover:text-red-700 cart_remove_form" href="{{ route('front.clearAll',['id'=>$ikey])}}">
                                            <i class="fas fa-trash text-sm sm:text-base"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Right Column - Order Summary -->
        
        <div class="lg:col-span-1">
            <div class="shadow-sm border rounded-lg p-4">
                <!-- Promotions Section -->
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.promotions') }}</h2>
                    <div class="flex gap-2">
                        <input type="text" placeholder="Enter Store/AS Code"
                            class="flex-1 border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" id="coupon_code">
                        <button id="coupon_apply" type="button" data-href="{{ route('front.getCouponDiscount')}}"
                         class="bg-blue-500 text-white px-6 py-2 rounded-md font-medium">{{ __('messages.apply') }}</button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-gray-800 mb-4">{{ __('messages.order_summary') }}</h2>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ __('messages.items_total') }} ({{getTotalCart()}} {{ __('messages.items') }})</p>
                            <p class="text-gray-800 font-medium">
                                <span class="sub_total" data-totalamount="{{getTotalAmount() -$cdiscount}}" data-totalvendor="{{totalVendorCart()}}">{{priceFormate(getTotalAmount())}}</span>
                            </p>
                        </div>
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ __('messages.delivery_fee') }}

                                @if(totalVendorCart()>1)
                                ({{totalVendorCart()}} Vendors Product)
                                @endif
                            </p>
                            <p class="text-gray-800 font-medium">Tk <span class="charge">0</span></p>
                        </div>

                        @if($cdiscount)
                        <div class="flex justify-between">
                            <p class="text-gray-600">Coupon Discount</p>
                            <p class="text-gray-800 font-medium"><span>{{ priceFormate($cdiscount)}}</span></p>
                        </div>
                        @endif
                        
                        @if(getCartDiscount())
                        <div class="flex justify-between">
                            <p class="text-gray-600"> Discount</p>
                            <p class="text-gray-800 font-medium"><span>{{ priceFormate(getCartDiscount())}}</span></p>
                        </div>
                        @endif
                        
                        
                        <div class="border-t border-gray-200 my-3 pt-3"></div>
                        <div class="flex justify-between">
                            <p class="text-gray-800 font-medium">{{ __('messages.total') }}:</p>
                            <p class="font-bold">Tk <span class="total_amount"> {{priceFormate(getTotalAmount() - $cdiscount)}} </span> </p>
                        </div>
                        <p class="text-gray-500 text-sm text-right">{{ __('messages.vat_included') }}</p>
                    </div>
                </div>

                <!-- Proceed to Pay Button -->
                
                <div class="flex justify-end space-x-3">
                    <!-- Cart Button -->
                    <a href="{{ route('front.carts.index') }}" 
                       class="bg-blue-500 text-white px-4 py-2 rounded-md text-center hover:bg-blue-600 transition">
                        {{ __('messages.cart') }}
                    </a>
                
                    <!-- Cancel Order Button -->
                    <a href="{{ route('front.clearAll') }}" 
                       class="bg-red-500 text-white px-4 py-2 rounded-md text-center hover:bg-red-600 transition cart_remove_form">
                        {{ __('messages.cancel_order') }}
                    </a>
                
                    <!-- Confirm Order Button -->
                    <button type="submit" form="checkout_form" 
                            class="bg-blue-500 text-white px-4 py-2 rounded-md text-center hover:bg-blue-600 transition">
                        {{ __('messages.confirm_order') }}
                    </button>
                </div>

            </div>
        </div>