<div class="flex flex-col h-full">
    <!-- Header -->
    <div class="flex justify-between items-center p-6 border-b border-gray-200">
        <h2 class="text-xl font-bold text-gray-800">{{ __('messages.your_shopping_cart') }}</h2>
        <button onclick="closeCartModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <!-- Cart Items -->
    <div id="cartItems" class="flex-1 overflow-y-auto p-6 space-y-4">
        <!-- Cart Item 1 -->

        @php
          $cart = session()->get('cart', []);
          $total=0;
       @endphp
       @if($cart)
       
       @foreach($cart as $key=>$item)
           @php
           $total +=$item['price']*$item['quantity']; 
           @endphp

        <div class="flex items-center border-b border-gray-200 pb-4">
            <img src="{{ $item['image']}}"
                alt="Joyroom Shower" class="w-16 h-16 object-cover rounded-md mr-4">
            <div class="flex-1">
                <h3 class="text-gray-800 font-medium">{{ $item['name']}}</h3>
                @if($item['variation_name'] !='dummy')
                <p class="text-gray-600 text-sm">{{ $item['variation_name']}}</p>
                @endif
                <p class="text-orange-500 font-medium"> {{ priceFormate($item['price'])}} 
                
                @if($item['discount'] >0)
                    <span class="line-through text-gray-400 text-xs"> {{$item['old_price'] }} </span>
                @endif
                </p>
            </div>
            <div class="flex items-center" data-href="{{ route('front.carts.edit',[$key])}}">
                <button type="button" class="qtybtn px-2 sm:px-3 py-1 text-gray-500 text-sm  border">-</button>
                <input type="text" id="quantity-{{$key}}" value="{{ $item['quantity']}}" min="1" data-max="{{ $item['qty_available'] }}"
                    class="w-8 sm:w-10 text-center border py-1 text-xs sm:text-sm">
                <button type="button" class="qtybtn px-2 sm:px-3 py-1 text-gray-500 text-sm border quantity-plus mr-2">+</button>

                <form action="{{ route('front.carts.destroy',[$key])}}" id="cart_remove_form" method="post">
                    @method('DELETE')
                    @csrf
                    <button type="submit" class="text-red-500 hover:text-red-700">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                
            </div>
        </div>
        @endforeach

        @else
        <!-- Empty Cart Message (Hidden by default) -->
        <div id="emptyCartMessage" class="text-center text-gray-600">
            {{ __('messages.your_cart_is_empty') }}
        </div>
        @endif
    </div>
    <!-- Footer -->
    <div class="p-6 border-t border-gray-200">
        <div class="flex justify-between text-gray-800 font-medium mb-4">
            <span>{{ __('messages.total') }}:</span>
            <span id="cartTotal"> {{ priceFormate($total)}} </span>
        </div>
        <div class="flex justify-end space-x-2">
            <a href="{{ route('front.carts.index')}}"
                class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">{{ __('messages.proceed_to_cart') }}</a>
            <a href="{{ route('front.checkouts.index')}}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">{{ __('messages.proceed_to_checkout') }}</a>
        </div>
    </div>
</div>