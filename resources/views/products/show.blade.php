@extends('layouts.app')
@push('css')

@php

    $p_price=getProductDiscount($product);
    
    $discount_price=$p_price['discount_price'];
    $discount=$p_price['discount'];

@endphp

<link rel="stylesheet" href="{{ asset('assets/css/pages/product-details.css')}}">
    <style>
        .colors.active{
            border: 2px solid #3b82f6 !important;
        }
        .sizes.active {
            background-color: #d9d9d9 !important;
            border: 2px solid #3b82f6 !important;
        }
        
        .addtocart.disabled {
            cursor: not-allowed;
        }
    </style>
@endpush

@section('content')

<noscript>
    <style>
        .slider-for,
        .slider-nav {
            display: none;
        }

        .noscript-fallback {
            display: block;
        }
    </style>
    <div class="noscript-fallback container mx-auto px-4 md:px-6 py-4">
        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
            alt="Joyroom Shower" class="w-full h-auto rounded-md" loading="lazy">
    </div>
</noscript>

<!-- Breadcrumbs -->
    <div class="container mx-auto px-4 md:px-6 py-2 pt-6">
        <div class="flex items-center text-sm text-gray-500">
            <a href="#" class="hover:text-blue-600">Home</a>
            <span class="mx-1">></span>
            {{-- <a href="#" class="hover:text-blue-600">Sanitary</a>
            <span class="mx-1">></span>
            <a href="#" class="hover:text-blue-600">Fix</a>
            <span class="mx-1">></span> --}}
            <span class="text-gray-700">{{ $product->name}}</span>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 md:px-6 py-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Images with Slick Slider -->
            <div class="lg:col-span-2 md:pt-1">
                <div class="slider-for mb-4" role="region" aria-label="Product image gallery">
                    <div>
                        <img src="{{ $product->image_url}}"
                            alt="Joyroom Shower" class="w-full h-auto rounded-md" loading="lazy">
                    </div>
                    @foreach($product->images as $image)
                    <div>
                        <img src="{{ $image->image_url}}"
                            alt="Joyroom Shower" class="w-full h-auto rounded-md" loading="lazy">
                    </div>
                    @endforeach
                </div>

                <div class="slider-nav">
                    <div class="px-1">
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <img src="{{ $product->image_url}}"
                                alt="Thumbnail 1" class="w-full h-auto" loading="lazy">
                        </div>
                    </div>
                    @foreach($product->images as $image)
                    <div class="px-1">
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <img src="{{ $image->image_url}}"
                                alt="Thumbnail 2" class="w-full h-auto" loading="lazy">
                        </div>
                    </div>
                    @endforeach
                    
                </div>
            </div>

            <!-- Product Info --> 
            <div class="lg:col-span-1">
                <h1 class="text-xl font-medium mb-3"> {{ $product->name}} </h1>

                <div class="flex items-center mb-3">
                    <div class="flex items-center text-yellow-400 mr-2">
                        <i class="fas fa-star"></i>
                        <span class="ml-1 text-gray-800">  {{ $product->reviews->avg('review')}} </span>
                    </div>
                    <span class="text-gray-500 text-sm">({{$product->reviews->count()}} {{ __('messages.reviews') }})</span>
                    <button class="ml-auto">
                        <i class="fas fa-share-alt text-gray-500"></i>
                    </button>
                </div>

                <div class="mb-3">
                    <span class="text-sm text-gray-600">{{ __('messages.brands') }}:</span>
                    <span class="ml-1 text-blue-600">{{ $product->brand->name ??''}}</span>
                    <a href="{{ route('front.products.index',['brand_id' => $product->brand_id]) }}" class="ml-2 text-xs text-blue-600">{{ __('messages.more_items_from_unicon') }}</a>
                </div>
                
                <div class="mb-4">
                    <div class="flex items-baseline space-x-2">
                
                        @if($discount_price > 0)
                            {{-- Discounted Price --}}
                            <span class="text-red-600 text-2xl font-semibold">
                                <span class="product_price">
                                    {{ priceFormate($product->sell_price - $discount_price) }}
                                </span>
                            </span>
                
                            {{-- Original Price --}}
                            <span class="text-gray-500 line-through text-2xl">
                                {{ priceFormate($product->sell_price) }}
                            </span>
                          
                            @if($discount_price > 0)
                                <span class="bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded shadow">
                                    {{ number_format($discount->amount, 0) }}
                                    {{ $discount->discount_type == 'percentage' ? '%' : 'tk' }} off
                                </span>
                            @endif
                        @else
                            {{-- Normal Price --}}
                            <span class="text-red-600 text-2xl font-semibold">
                                <span class="product_price">
                                    {{ priceFormate($product->sell_price) }}
                                </span>
                            </span>
                        @endif
                
                    </div>
                </div>



                @if($product->type=='variable')
                    @php
                    $array=[];
                    if ($product->variants) {
                        $array=json_decode($product->variants,true);
                    }
                    @endphp

                    @foreach($array as $key => $arrayn)
                    @foreach($arrayn as $vname => $sizes)
                        <div class="mb-4 <?php echo strtolower($vname); ?>">
                            <div class="text-sm text-gray-600 mb-1">{{$vname}}</div>
                            <div class="flex space-x-2">
                                @foreach($sizes as $key => $color)
                                @if(strtolower($vname)=='color')
                                <button class="colors h-8 w-8 border border-gray-400 rounded-full" style="background-color: {{ strtolower($color) }};">{{$color}}</button>
                                @elseif(strtolower($vname)=='size')
                                <button class="sizes px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-100 ">{{$color}}</button>
                                @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    @endforeach
                @endif

                {{--
                <form action="{{ route('front.carts.store')}}" method="post" id="cart_form">
                    @csrf
                    <div class="mb-6">
                        <div class="text-sm text-gray-600 mb-1">{{ __('messages.quantity') }}</div>
                        <div class="flex items-center">
                            <button  class="border border-gray-300 px-3 py-1 rounded-l">-</button>
                            <input type="text" value="1" name="quantity" class="border-t border-b border-gray-300 w-12 py-1 text-center">
                            <button class="border border-gray-300 px-3 py-1 rounded-r">+</button>
                        </div>

                        <input type="hidden" name="product_id" value="{{ $product->id}}">
                        @if($product->type=='single')
                            <input type="hidden" name="variation_id" id="variation_id" value="{{ $product->variation->id}}">
                        @else
                            <input type="hidden" name="variation_id" id="variation_id" value="">
                        @endif
                        
                    </div>

                    <div class="flex space-x-3 mb-6">
                        <button name="action" value="cart" type="submit" class="flex-1 addtocart">
                            <div class="bg-white border border-gray-300 text-gray-800 px-4 py-3 rounded-md text-center">
                                {{ __('messages.add_to_cart') }}
                            </div>
                        </button>
                        <button name="action" value="buy" type="submit" class="flex-1 addtocart">
                            <div class="bg-blue-500 text-white px-4 py-3 rounded-md text-center">
                                {{ __('messages.buy_now') }}
                            </div>
                        </button>
                    </div>
                </form>
                --}}
                @php
                    // ১. সেশন থেকে কার্ট ডেটা নেওয়া
                    $cart = session()->get('cart', []);
                    
                    // ২. কারেন্ট প্রোডাক্টের Varation ID বের করা
                    $currentVariationId = ($product->type == 'single') ? optional($product->variation)->id : null;
                    
                    // ৩. সেশন কার্টে এই প্রোডাক্ট/ভ্যারিয়েশন আছে কি না চেক করা
                    $existingQty = 1; // Default Quantity
                    
                    if (!empty($cart)) {
                        foreach ($cart as $item) {
                            // Variable product বা Single product এর সাথে Match করা
                            if (isset($item['variation_id']) && $item['variation_id'] == $currentVariationId) {
                                $existingQty = $item['quantity'];
                                break;
                            } elseif (isset($item['product_id']) && $item['product_id'] == $product->id && $product->type == 'single') {
                                $existingQty = $item['quantity'];
                                break;
                            }
                        }
                    }
                @endphp

                <form action="{{ route('front.carts.store')}}" method="post" id="cart_form">
                    @csrf
                    
                    <div class="mb-6">
                        <div class="text-sm text-gray-600 mb-1">{{ __('messages.quantity') }}</div>
                        <div class="flex items-center">
                            <button type="button" id="qty_decrement" class="border border-gray-300 px-3 py-1 rounded-l text-gray-700 hover:bg-gray-100">-</button>
                            
                            <!-- dynamic value set (Page reload হলেও কার্টের Quantity ইম্পোর্ট হবে) -->
                            <input type="text" id="product_quantity" value="{{ $existingQty }}" name="quantity" min="1" class="border-t border-b border-gray-300 w-12 py-1 text-center text-gray-800">
                            
                            <button type="button" id="qty_increment" class="border border-gray-300 px-3 py-1 rounded-r text-gray-700 hover:bg-gray-100">+</button>
                        </div>

                        <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variation_id" id="variation_id" value="{{ $currentVariationId }}">
                    </div>

                    <div class="flex space-x-3 mb-6">
                        <button name="action" value="cart" type="submit" class="flex-1 addtocart">
                            @if ($existingQty > 1)
                                <div class="bg-white border border-gray-300 text-gray-800 px-4 py-3 rounded-md text-center">
                                    {{ __('messages.updated_to_cart') }}
                                </div>
                                @else
                                <div class="bg-white border border-gray-300 text-gray-800 px-4 py-3 rounded-md text-center">
                                    {{ __('messages.add_to_cart') }}
                                </div>
                            @endif
                        </button>
                        <button name="action" value="buy" type="submit" class="flex-1 addtocart">
                            <div class="bg-blue-500 text-white px-4 py-3 rounded-md text-center">
                                {{ __('messages.buy_now') }}
                            </div>
                        </button>
                    </div>
                </form>

                @if($product->user)

                @php
                    $add=$product->user->vendorAddress;
                @endphp
                <!-- Sold by -->
                <div class="border border-gray-200 rounded-md p-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <div class="text-sm font-medium">{{ __('messages.sold_by') }}</div>
                        @if ($add->slug)
                            <a href="{{ route('front.shop', $add->slug) }}" class="text-blue-600 text-sm flex items-center">
                                {{$add->shop_name?? $add->name}}
                                <i class="fas fa-chevron-right ml-1 text-xs"></i>
                            </a>
                            @else
                            <label for="">< {{ $add->shop_name ? $add->name : 'N/A'}}/label>
                        @endif
                    </div>

                    <!-- Ship to -->
                    <div class="mb-4">
                        <div class="text-sm font-medium mb-2">{{ __('messages.ship_to') }}</div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-500 mt-1 mr-2"></i>
                            <div>
                                <div class="text-sm"> {{$add->address}} </div>
                            </div>
                            <!--<a href="#" class="ml-auto text-blue-600 text-xs">{{ __('messages.change') }}</a>-->
                        </div>
                    </div>

                    <!-- Standard Delivery -->
                    
                    @if($product->estimate_delivery_day)
                    
                    <div class="mb-4">
                        <div class="text-sm font-medium mb-2">{{ __('messages.standard_delivery') }}</div>
                        <div class="flex items-start">
                            <i class="far fa-calendar-alt text-gray-500 mt-1 mr-2"></i>
                            <div class="text-sm">Estimated by {{ date("d-Y, M", strtotime(date('Y-m-d') . " +" . $product->estimate_delivery_day . " day")) }}</div>
                            <div class="ml-auto text-sm">Tk 150</div>
                        </div>
                    </div>
                    @endif

                    <!-- Cash On Delivery -->
                    <!--<div class="mb-4">-->
                    <!--    <div class="text-sm font-medium mb-2">{{ __('messages.cash_on_delivery') }}</div>-->
                    <!--    <div class="flex items-start">-->
                    <!--        <i class="fas fa-check-circle text-gray-500 mt-1 mr-2"></i>-->
                    <!--        <div class="text-sm">{{ __('messages.available') }}</div>-->
                    <!--    </div>-->
                    <!--</div>-->

                    <!-- Return & Warranty -->
                    
                    @if($product->warranty_available || $product->return_available)
                    <div>
                        <div class="text-sm font-medium mb-2">{{ __('messages.return_warranty') }}</div>
                        
                        @if($product->warranty_available)
                        <div class="flex items-start mb-2">
                            <i class="fas fa-undo text-gray-500 mt-1 mr-2"></i>
                            <div class="text-sm">Days : {{ $product->warranty_days}} </div>
                            <div class="text-sm ml-4">  Note: {{$product->warranty_note}}</div>
                        </div>
                        @endif
                        
                        @if($product->return_available)
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-gray-500 mt-1 mr-2"></i>
                            <div class="text-sm">Days : {{ $product->return_days}} </div>
                            <div class="text-sm ml-4">  Note: {{$product->return_note}}</div>
                        </div>
                        @endif
                    </div>
                    @endif
                    
                    
                </div>
                @endif

            </div>
        </div>

        <!-- Product Description -->
        <div class="mt-8">
            <h2 class="text-xl font-medium mb-4">{{ $product->name}}</h2>

            <!-- Tab Navigation -->
            <div class="overflow-x-auto">
                <div class="flex border-b border-gray-200">
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:bg-gray-300"
                        data-tab="description">{{ __('messages.description') }}</button>
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:bg-gray-300"
                        data-tab="specification">{{ __('messages.specification') }}</button>
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:bg-gray-300"
                        data-tab="image">{{ __('messages.image') }}</button>
                    @if(!empty($product->video_link))
                    <button
                        class="px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 focus:outline-none focus:bg-gray-300"
                        data-tab="video">{{ __('messages.video') }}</button>
                    @endif
                </div>
            </div>

            <!-- Tab Content -->
            <div id="tab-content">
                <!-- Description Tab -->
                <div class="tab-pane p-4" data-tab-content="description">
                    {!! $product->description !!}
                </div>

                <!-- Specification Tab -->
                <div class="tab-pane hidden p-4" data-tab-content="specification">
                    {!! $product->specification !!}
                </div>

                <!-- Image Tab -->
                <div class="tab-pane hidden p-4" data-tab-content="image">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.image') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Test Image 1 -->
                        

                        @foreach($product->images as $image)
                        <div class="relative overflow-hidden rounded-lg shadow-md">
                            <img src="{{ $image->image_url}}"
                                alt="Bathroom Shower Image 1"
                                class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        @endforeach
                        <!-- Test Image 2 -->
                        
                    </div>
                </div>

                <!--Video Tab -->
                @if(!empty($product->video_link))
                    <div class="tab-pane hidden p-4" data-tab-content="video">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.video') }}</h3>
                        <div class="relative w-full max-w-2xl mx-auto">
                            
                            <iframe class="w-full h-64 md:h-96 rounded-lg shadow-md"
                                src="https://www.youtube.com/embed/{{ $product->video_link }}"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    
                            <p class="text-sm text-gray-600 mt-2 text-center">Watch the Joyroom Shower 12.4 in action!</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Reviews -->

        <!--hide for Custom order placed form-->
        {{---
        <div class="mt-8">
            <h2 class="text-xl font-medium mb-4">{{ __('messages.product_reviews') }}</h2>
            <!-- Review 1 -->
            

            <!-- Review 2 -->
            <div class="review_list">
                @include('products.reviewList',['product'=>$product])
            </div>
        </div>

        <div class="mx-auto mb-8 md:mb-12">
            <div class="flex flex-col md:flex-row md:justify-between md:space-x-6 space-y-6 md:space-y-0">
              <!-- Customer Reviews -->
              <div class="w-full md:w-1/2 bg-white p-4 md:p-6 rounded-lg shadow-md">
                <div class="flex items-center justify-between mb-4">
                  <h2 class="text-lg md:text-xl font-medium text-gray-800">{{ __('messages.customer_reviews') }}</h2>
                </div>
          
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400 w-20 md:w-24">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                  </div>
                  <div class="w-32 md:w-48 bg-gray-200 rounded-full h-2.5 mx-2">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 70%"></div>
                  </div>
                  <span class="text-xs md:text-sm text-gray-500">70%</span>
                </div>
          
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400 w-20 md:w-24">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                  <div class="w-32 md:w-48 bg-gray-200 rounded-full h-2.5 mx-2">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 10%"></div>
                  </div>
                  <span class="text-xs md:text-sm text-gray-500">10%</span>
                </div>
          
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400 w-20 md:w-24">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                  <div class="w-32 md:w-48 bg-gray-200 rounded-full h-2.5 mx-2">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 7%"></div>
                  </div>
                  <span class="text-xs md:text-sm text-gray-500">7%</span>
                </div>
          
                <div class="flex items-center mb-2">
                  <div class="flex text-yellow-400 w-20 md:w-24">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                  <div class="w-32 md:w-48 bg-gray-200 rounded-full h-2.5 mx-2">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 12%"></div>
                  </div>
                  <span class="text-xs md:text-sm text-gray-500">12%</span>
                </div>
          
                <div class="flex items-center mb-4">
                  <div class="flex text-yellow-400 w-20 md:w-24">
                    <i class="fas fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                    <i class="far fa-star"></i>
                  </div>
                  <div class="w-32 md:w-48 bg-gray-200 rounded-full h-2.5 mx-2">
                    <div class="bg-yellow-400 h-2.5 rounded-full" style="width: 0%"></div>
                  </div>
                  <span class="text-xs md:text-sm text-gray-500">0%</span>
                </div>
          
                <div class="flex items-center text-xs md:text-sm mb-6">
                  <div class="flex text-yellow-400 mr-2">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                  </div>
                  <span class="text-2xl md:text-3xl font-bold text-gray-800">{{ $product->reviews->avg('review')}} / 5</span>
                  <span class="ml-2 text-gray-500">({{ $product->reviews->sum('review')}} {{ __('messages.ratings') }})</span>
                </div>
              </div>
          
              <!-- Create Review -->
              <div class="w-full md:w-1/2 bg-white p-4 md:p-6 rounded-lg shadow-md">
                <h2 class="text-xl md:text-2xl font-semibold text-gray-800 mb-6">{{ __('messages.create_review') }}</h2>
                <form action="{{ route('front.product-reviews.store')}}" method="post" id="review_form">
                    @csrf
                <!-- Overall Rating Section -->
                <div class="mb-6">
                  <label class="block text-gray-700 font-medium mb-2 text-sm md:text-base">{{ __('messages.overall_rating') }}</label>
                  <div class="flex space-x-1" id="rating">
                    <i class="fas fa-star w-5 h-5 md:w-6 md:h-6 cursor-pointer text-gray-300" id="star1"></i>
                    <i class="fas fa-star w-5 h-5 md:w-6 md:h-6 cursor-pointer text-gray-300" id="star2"></i>
                    <i class="fas fa-star w-5 h-5 md:w-6 md:h-6 cursor-pointer text-gray-300" id="star3"></i>
                    <i class="fas fa-star w-5 h-5 md:w-6 md:h-6 cursor-pointer text-gray-300" id="star4"></i>
                    <i class="fas fa-star w-5 h-5 md:w-6 md:h-6 cursor-pointer text-gray-300" id="star5"></i>
                  </div>
                </div>
          
                <!-- Your Name Section -->
                <div class="mb-6">
                    <input type="hidden" name="review" id="review">
                    <input type="hidden" name="product_id" value="{{ $product->id}}">
                  <label class="block text-gray-700 font-medium mb-2 text-sm md:text-base">{{ __('messages.your_name') }}</label>
                  <input
                    name='name'
                    type="text"
                    placeholder="{{ __('messages.your_name') }}"
                    class="w-full p-2 md:p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                  >
                </div>
          
                <!-- Add a Photo Section -->
                <div class="mb-6">
                  <label class="block text-gray-700 font-medium mb-2 text-sm md:text-base">{{ __('messages.add_photo') }}</label>
                  <div class="flex items-center space-x-4">
                    <input type="file" class="form-comtrol" name="image">
                  </div>
                </div>
          
                <!-- Add a Written Review Section -->
                <div class="mb-6">
                  <label class="block text-gray-700 font-medium mb-2 text-sm md:text-base">{{ __('messages.add_written_review') }}</label>
                  <textarea name="message" 
                    placeholder="{{ __('messages.review_feedback') }}"
                    class="w-full p-2 md:p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 h-24 md:h-32 resize-none text-sm md:text-base"
                  ></textarea>
                </div>
          
                <!-- Submit Button -->
                <div class="mt-6 text-start">
                  <button
                    type="submit"
                    class="px-4 py-1 px-4 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base"
                  >
                    {{ __('messages.submit_review') }}
                  </button>
                </div>

                </form>
              </div>

            </div>
        </div>
        --}}

        <br/><br/><br/>

        <!-- Similar Products -->
        <div class="mb-8 md:mb-12">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg md:text-xl font-bold">{{ __('messages.similar_products') }}</h3>
                <a href="{{ route('front.products.index',['cat_id'=>$product->category_id])}}"
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
    </main>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
        $(document).ready(function() {
            // -----------------------------------------------------------
            // A. Single Product Page-এর নিজের + এবং - বাটন লজিক
            // -----------------------------------------------------------
            $('#qty_increment').on('click', function(e) {
                e.preventDefault();
                let qtyInput = $('#product_quantity');
                qtyInput.val((parseInt(qtyInput.val()) || 1) + 1);
            });

            $('#qty_decrement').on('click', function(e) {
                e.preventDefault();
                let qtyInput = $('#product_quantity');
                let currentVal = parseInt(qtyInput.val()) || 1;
                if (currentVal > 1) {
                    qtyInput.val(currentVal - 1);
                }
            });

            $('#product_quantity').on('change blur', function() {
                let val = parseInt($(this).val());
                if (isNaN(val) || val < 1) $(this).val(1);
            });

            // -----------------------------------------------------------
            // B. Real-time Event Listener (পপআপ কার্ট চেঞ্জ হলে এটি রান হবে)
            // -----------------------------------------------------------
            $(document).on('cartUpdated', function(event, cartData) {
                let currentProductId = $('#product_id').val();
                let currentVariationId = $('#variation_id').val();
                
                let foundInCart = false;

                // পপআপের আপডেটেড কার্ট ডাটা থেকে বর্তমান প্রোডাক্টটি খোঁজা
                if (cartData) {
                    $.each(cartData, function(key, item) {
                        // ভ্যারিয়েশন বা প্রোডাক্ট আইডি ম্যাচিং
                        if ((currentVariationId && item.variation_id == currentVariationId) || 
                            (item.product_id == currentProductId)) {
                            
                            // ম্যাচ করলে সিঙ্গেল পেজের ইনপুট ফিল্ডের ভ্যালু আপডেট
                            $('#product_quantity').val(item.quantity);
                            foundInCart = true;
                            return false; // Break loop
                        }
                    });
                }

                // যদি পপআপ কার্ট থেকে প্রোডাক্টটি Delete করে দেওয়া হয়, তবে Quantity ১ করে দেওয়া
                if (!foundInCart) {
                    $('#product_quantity').val(1);
                }
            });
        });

    $(document).ready(function(){

        checkVariation();
    });

    function checkVariation(){

        let variation_id=$(document).find('#variation_id').val() || 0;
        if(variation_id==0){
            $('.addtocart').addClass('disabled').prop("disabled", true);
        }else{
            $('.addtocart').removeClass('disabled').prop("disabled", false);
        }
    }



    function getVariation(){
        let url ='{{ route("front.getVariation",[$product->id])}}';
        let color=$(document).find('.colors.active').text() || null;
        let size=$(document).find('.sizes.active').text() || null;
        $.ajax({
            url: url,
            method: 'GET',
            data:{size,color},
            dataType :"JSON",
            success: function (res) {

                if(res.variation){
                    $('#variation_id').val(res.variation.id);
                    $('.product_price').text(res.variation.sell_price);
                }
                
                checkVariation();
            }
        });
    }

    $('.sizes').on('click', function(){
        $('.sizes').removeClass('active');
        $(this).addClass('active');
    });

    $('.colors').on('click', function(){
        $('.colors').removeClass('active');
        $(this).addClass('active');
    });

    $('.sizes, .colors').on('click', function(){
        setTimeout(function() {
            getVariation();
        }, 100);
    });
    
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

    // JavaScript for tab functionality
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('[data-tab]');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                // Remove active state from all tabs
                tabs.forEach(t => t.classList.remove('bg-gray-200', 'focus:bg-gray-300'));
                // Add active state to clicked tab
                this.classList.add('bg-gray-200', 'focus:bg-gray-300');

                // Hide all panes
                panes.forEach(pane => pane.classList.add('hidden'));
                // Show the corresponding pane
                const tabContent = document.querySelector(`.tab-pane[data-tab-content="${this.getAttribute('data-tab')}"]`);
                tabContent.classList.remove('hidden');
            });
        });

        // Activate the first tab by default
        tabs[0].click();
    });

    // review star
    const stars = document.querySelectorAll('i.fas.fa-star');
    stars.forEach(star => {
        star.addEventListener('click', function () {
            const starId = parseInt(this.id.replace('star', ''));
            $('#review').val(starId);

            stars.forEach(s => {
                const sId = parseInt(s.id.replace('star', ''));

                if (sId <= starId) {
                    s.classList.add('text-yellow-400');
                    s.classList.remove('text-gray-300');
                } else {
                    s.classList.remove('text-yellow-400');
                    s.classList.add('text-gray-300');
                }
            });
        });
    });
</script>

@endpush