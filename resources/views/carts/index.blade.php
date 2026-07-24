@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col lg:flex-row gap-6 md:pt-8">
        <!-- Left Side - Cart Items -->
        <div class="w-full">
            <!-- Select All -->
            <div class="flex items-center justify-between border-b pb-3 mb-4 sm:mb-6 px-4">
                <div class="flex items-center">
                    <label for="selectAll" class="text-gray-800 text-sm sm:text-base">{{ __('messages.you_select') }} (<span id="itemsCountNumber">{{getTotalCart()}}</span> {{ __('messages.items') }})</label>
                </div>
                <a href="{{ route('front.clearAll')}}"
                    class="cart_remove_form text-red-500 flex items-center text-sm sm:text-base hover:text-red-600 transition-colors">
                    <i class="fas fa-trash mr-1 sm:mr-2"></i>
                    {{ __('messages.delete') }}
                </a>
            </div>

            <!-- Store 1 -->

            
            @foreach($grouped as $userid=>$gritem)
            @php
                $user=\App\Models\User::find($userid);
            @endphp
            <div class="mb-4 sm:mb-6 border rounded-lg p-3 sm:p-4 md:p-5">
                <div class="flex items-center mb-3 sm:mb-4">
                    <i class="fas fa-store mr-2 text-sm sm:text-base"></i>
                    <span class="font-medium text-sm sm:text-base"> {{ $user->name}} </span>
                </div>

                @foreach($gritem as $item)

                <!-- Product 1 -->  
                <div data-variation-id="{{ $item['variation_id'] }}" class="flex flex-col sm:flex-row border-t border-b py-3 sm:py-4 gap-3 sm:gap-4">
                    <div class="flex items-start">
                        <div class="w-16 h-16 sm:w-20 sm:h-20 md:w-24 md:h-24 bg-gray-200 mr-2 sm:mr-3 shrink-0">
                            <img src="{{ $item['image']}}"
                                alt="Joyroom Shower" class="w-full h-full object-cover rounded-md">
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm sm:text-base md:text-lg font-medium mb-1">{{ $item['name']}}</h3>
                        <p class="text-gray-600 mb-1 text-xs sm:text-sm"> {{ $item['variation_name'] =='dummy'?'':$item['variation_name']}}</p>
                        <p class="text-red-500 text-xs sm:text-sm">Only {{ $item['qty_available']}} item(s) in stock</p>
                    </div>
                    <div class="flex flex-col sm:items-end justify-between gap-2">
                        <div>
                            <div class=" font-bold text-sm sm:text-base md:text-lg"> {{ priceFormate($item['price'])}} </div>
                            @if($item['discount'] >0)
                                <div class="text-gray-400 line-through text-xs sm:text-sm"> {{ priceFormate($item['old_price']) }} </div> 
                            @endif
                
                            
                        </div>
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex items-center border rounded overflow-hidden" data-href="{{ route('front.carts.edit',[$item['variation_id']])}}">
                                <button class="px-2 sm:px-3 py-1 text-gray-500 text-sm cart_qtybtn" >-</button>
                                <input type="text" value="{{ $item['quantity']}}" name="quantity" data-max="{{ $item['qty_available'] }}"
                                    class="w-8 sm:w-10 text-center border-x py-1 text-xs sm:text-sm">
                                <button class="px-2 sm:px-3 py-1 text-gray-500 text-sm cart_qtybtn quantity-plus" >+</button>
                            </div>
                            <a class="text-red-500 cart_remove_form" href="{{ route('front.clearAll',['id'=>$item['variation_id'],'url'=>'url'])}}">
                                <i class="fas fa-trash text-sm sm:text-base"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach


            </div>
            @endforeach

            <!-- Store 2 -->
        </div>

        <!-- Right Side - Order Summary -->
        <div class="w-full lg:w-1/3">
            <div class="bg-white p-4 rounded-lg shadow-sm border sm:p-6">
                <!-- Shipping Address -->
                <!-- <div class="mb-6">
                    <h3 class="text-base font-medium mb-3 sm:text-lg sm:mb-4">Ship to</h3>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt mt-1 mr-2 text-gray-500 text-sm sm:text-base"></i>
                        <div>
                            <p class="text-gray-800 text-sm sm:text-base">Dhaka, Tangail-Mohera, Bhatkura, Mirzapur
                            </p>
                        </div>
                        <button class="ml-auto text-blue-500 font-medium text-sm sm:text-base">CHANGE</button>
                    </div>
                </div> -->

                <!-- Invoice and Contact -->
                <div class="">
                    <h3 class="text-base font-medium mb-2 sm:text-lg">{{ __('messages.invoice_and_contact_info') }}</h3>
                </div>

                <!-- Order Summary -->
                <div class="mb-6 border-t pt-4 sm:pt-6">
                    <h3 class="text-base font-medium mb-3 sm:text-lg sm:mb-4">{{ __('messages.order_summary') }}</h3>
                    <div class="flex justify-between mb-2 sm:mb-3">
                        <span class="text-gray-700 text-sm sm:text-base">{{ __('messages.items_total') }} (<span id="orderItemsCount">{{getTotalCart()}}</span> {{ __('messages.items') }})</span>
                        <span id="orderItemsAmount" class="font-medium text-sm sm:text-base"> {{priceFormate(getTotalAmount())}} </span>
                    </div>
                    <!--<div class="flex justify-between mb-2 sm:mb-3">-->
                    <!--    <span class="text-gray-700 text-sm sm:text-base">{{ __('messages.delivery_fee') }}</span>-->
                    <!--    <span class="font-medium text-sm sm:text-base">Tk 0</span>-->
                    <!--</div>-->
                </div>

                <!-- Promotions -->
                <div class="mb-6 border-t pt-4 sm:pt-6">
                    <h3 class="text-base font-medium mb-3 sm:text-lg sm:mb-4">{{ __('messages.promotions') }}</h3>
                    <div class="flex flex-col sm:flex-row mb-3 gap-2 sm:gap-0">
                        <input type="text" placeholder="Enter Store/AS Code"
                            class="flex-1 border rounded-t sm:rounded-l sm:rounded-t-none px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm sm:text-base" />
                        <button
                            class="bg-blue-500 text-white px-4 py-2 rounded-b sm:rounded-r sm:rounded-b-none font-medium text-sm sm:text-base">
                            {{ __('messages.apply') }}
                        </button>
                    </div>
                </div>

                <!-- Total -->
                <div class="mb-6 border-t pt-4 sm:pt-6">
                    <div class="flex justify-between mb-1">
                        <span class="text-base font-medium sm:text-lg">{{ __('messages.total') }}:</span>
                        <span id="pageTotal" class="font-bold text-xl sm:text-lg"> {{priceFormate(getTotalAmount())}} </span>
                    </div>
                    <p class="text-gray-500 text-xs text-right sm:text-sm">{{ __('messages.vat_included') }}</p>
                </div>

                <!-- Checkout Button -->
                <a href="{{ route('front.checkouts.index')}}" class="flex-1">
                    <div class="bg-blue-500 text-white px-4 py-3 rounded-md text-center">
                        {{ __('messages.proceed_to_checkout') }}
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="mb-8 md:mb-12 mt-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg md:text-xl font-bold">{{ __('messages.top_products') }}</h3>
                <a href="{{ route('front.products.index')}}"
                    class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                    <span>{{ __('messages.more_products') }}</span><i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach($products as $sproduct)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @include('products.section',['sproduct'=>$sproduct])
                </div>
                @endforeach
            </div>
        </div>
</div>
@endsection
@push('js')
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>


<script type="text/javascript">
    $(document).ready(function () {
        // Initialize the main slider (slider-for)
        $('.slider-for').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.slider-nav',
            adaptiveHeight: true,
            swipe: true,
            draggable: true,
            touchThreshold: 10
        });

        // Initialize the thumbnail slider (slider-nav)
        $('.slider-nav').slick({
            slidesToShow: 4,
            slidesToScroll: 1,
            asNavFor: '.slider-for',
            dots: false,
            centerMode: false,
            focusOnSelect: true,
            arrows: true,
            swipe: true,
            draggable: true,
            touchThreshold: 10,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4
                    }
                },
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 2
                    }
                }
            ]
        });

        // Fix for resize issues
        $(window).on('resize', function () {
            $('.slider-for').slick('resize');
            $('.slider-nav').slick('resize');
        });
    });
</script>

@endpush
