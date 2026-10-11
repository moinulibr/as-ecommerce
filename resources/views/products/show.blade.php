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
        .sizes.active,
        .variants.active {
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
            <span class="text-gray-700">{{ $product->name}}</span>
        </div>
    </div>

    <!-- Main Content -->
    <main class="container mx-auto px-4 md:px-6 py-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Images -->
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
                            <span class="text-red-600 text-2xl font-semibold">
                                <span class="product_price">
                                    {{ priceFormate($product->sell_price - $discount_price) }}
                                </span>
                            </span>
                
                            <span class="text-gray-500 line-through text-2xl product_old_price">
                                {{ priceFormate($product->sell_price) }}
                            </span>
                          
                            @if($discount_price > 0)
                                <span class="bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded shadow">
                                    {{ number_format($discount->amount, 0) }}
                                    {{ $discount->discount_type == 'percentage' ? '%' : 'tk' }} off
                                </span>
                            @endif
                        @else
                            <span class="text-red-600 text-2xl font-semibold">
                                <span class="product_price">
                                    {{ priceFormate($product->sell_price) }}
                                </span>
                            </span>
                        @endif
                
                    </div>
                </div>

                {{-- ============================================================ --}}
                {{-- VARIANT SELECTOR — FULLY DYNAMIC                                   --}}
                {{-- ============================================================ --}}
                @if($product->type=='variable')
                    @php
                    $array=[];
                    if ($product->variants) {
                        $array=json_decode($product->variants,true);
                    }
                    @endphp

                    @foreach($array as $key => $arrayn)
                    @foreach($arrayn as $vname => $sizes)
                        @php
                            $attrSlug = strtolower($vname);
                            // Color-এর নাম যাই হোক (color/colour/colors/colours) — swatch style
                            $isColor = in_array($attrSlug, ['color', 'colour', 'colors', 'colours']);
                        @endphp
                        <div class="mb-4 variant-group" data-attr-group="{{ $attrSlug }}">
                            <div class="text-sm text-gray-600 mb-1">{{ $vname }}</div>
                            <div class="flex space-x-2 flex-wrap">
                                @foreach($sizes as $color)
                                    @if($isColor)
                                        <button type="button"
                                                class="variant-btn colors h-8 w-8 border border-gray-400 rounded-full"
                                                style="background-color: {{ strtolower($color) }};"
                                                data-attr="{{ $attrSlug }}"
                                                data-value="{{ $color }}"
                                                title="{{ $color }}">{{ $color }}</button>
                                    @else
                                        <button type="button"
                                                class="variant-btn variants px-3 py-1 border border-gray-300 rounded-md text-sm hover:bg-gray-100"
                                                data-attr="{{ $attrSlug }}"
                                                data-value="{{ $color }}">{{ $color }}</button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                    @endforeach
                @endif

                
                @php
                    $cart = session()->get('cart', []);
                    $currentVariationId = ($product->type == 'single') ? optional($product->variation)->id : null;
                    $existingQty = 1;
                    
                    if (!empty($cart)) {
                        foreach ($cart as $item) {
                            if (isset($item['variation_id']) && $item['variation_id'] == $currentVariationId) {
                                $existingQty = $item['quantity'];
                                break;
                            } elseif (isset($item['product_id']) && $item['product_id'] == $product->id && $product->type == 'single') {
                                $existingQty = $item['quantity'];
                                break;
                            }
                        }
                    }
                    
                    $inCart = !empty($product->isAddedToCart);
                @endphp

                <form action="{{ route('front.carts.store')}}" method="post" id="cart_form" class="cart-form">
                    @csrf
                    
                    <div class="mb-6">
                        <div class="text-sm text-gray-600 mb-1">{{ __('messages.quantity') }}</div>
                        <div class="flex items-center">
                            <button type="button" id="qty_decrement" class="border border-gray-300 px-3 py-1 rounded-l text-gray-700 hover:bg-gray-100">-</button>
                            
                            <input type="text" id="product_quantity" value="{{ $existingQty }}" name="quantity" min="1" class="border-t border-b border-gray-300 w-12 py-1 text-center text-gray-800">
                            
                            <button type="button" id="qty_increment" class="border border-gray-300 px-3 py-1 rounded-r text-gray-700 hover:bg-gray-100">+</button>
                        </div>

                        <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="variation_id" id="variation_id" value="{{ $currentVariationId }}">
                    </div>

                    <div class="flex space-x-3 mb-6">
                        <button name="action" value="cart" type="submit"
                                class="flex-1 addtocart product-cart-btn"
                                data-product-id="{{ $product->id }}"
                                data-variation-id="{{ $currentVariationId }}"
                                data-add-text="{{ __('messages.add_to_cart') }}"
                                data-in-cart-text="{{ __('messages.updated_to_cart') }}">
                            <div class="bg-white border border-gray-300 text-gray-800 px-4 py-3 rounded-md text-center cart-btn-label">
                                @if ($inCart)
                                    {{ __('messages.updated_to_cart') }}
                                @else
                                    {{ __('messages.add_to_cart') }}
                                @endif
                            </div>
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

                    <div class="mb-4">
                        <div class="text-sm font-medium mb-2">{{ __('messages.ship_to') }}</div>
                        <div class="flex items-start">
                            <i class="fas fa-map-marker-alt text-gray-500 mt-1 mr-2"></i>
                            <div>
                                <div class="text-sm"> {{$add->address}} </div>
                            </div>
                        </div>
                    </div>

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

            <div id="tab-content">
                <div class="tab-pane p-4" data-tab-content="description">
                    {!! $product->description !!}
                </div>

                <div class="tab-pane hidden p-4" data-tab-content="specification">
                    {!! $product->specification !!}
                </div>

                <div class="tab-pane hidden p-4" data-tab-content="image">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('messages.image') }}</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach($product->images as $image)
                        <div class="relative overflow-hidden rounded-lg shadow-md">
                            <img src="{{ $image->image_url}}"
                                alt="Bathroom Shower Image 1"
                                class="w-full h-48 object-cover hover:scale-105 transition-transform duration-300">
                        </div>
                        @endforeach
                    </div>
                </div>

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

        <br/><br/><br/>

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

            $(document).on('cartUpdated', function(event, cartData) {
                let currentProductId = $('#product_id').val();
                let currentVariationId = $('#variation_id').val();
                
                let foundInCart = false;
                let foundQty = 0;

                if (cartData && Array.isArray(cartData)) {
                    $.each(cartData, function(key, item) {
                        if ((currentVariationId && item.variation_id == currentVariationId) || 
                            (item.product_id == currentProductId)) {
                            foundQty = item.quantity;
                            foundInCart = true;
                            return false;
                        }
                    });
                }
                
                if (cartData && cartData.variationIds) {
                    let vIds = (cartData.variationIds || []).map(String);
                    let pIds = (cartData.productIds || []).map(String);
                    if ((currentVariationId && vIds.includes(String(currentVariationId))) ||
                        (currentProductId && pIds.includes(String(currentProductId)))) {
                        foundInCart = true;
                    }
                }

                if (foundInCart && foundQty > 0) {
                    $('#product_quantity').val(foundQty);
                }
                if (!foundInCart) {
                    $('#product_quantity').val(1);
                }
                
                let $btn = $('#cart_form .product-cart-btn');
                if (foundInCart) {
                    $btn.find('.cart-btn-label').text($btn.data('in-cart-text') || 'Update Cart');
                } else {
                    $btn.find('.cart-btn-label').text($btn.data('add-text') || 'Add to Cart');
                }
            });
        });

    $(document).ready(function(){
        checkVariation();
    });

    function checkVariation(){
        let variation_id = $('#variation_id').val() || 0;
        if(variation_id == 0){
            $('.addtocart').addClass('disabled').prop("disabled", true);
        } else {
            $('.addtocart').removeClass('disabled').prop("disabled", false);
        }
    }

    // ============================================================
    // DYNAMIC VARIANT HANDLER — any attribute works
    // ============================================================
    $(document).on('click', '.variant-btn', function(){
        var $btn   = $(this);
        var $group = $btn.closest('.variant-group');

        $group.find('.variant-btn').removeClass('active');
        $btn.addClass('active');

        setTimeout(function() {
            getVariation();
        }, 100);
    });

    function getVariation(){
        var url = '{{ route("front.getVariation",[$product->id])}}';
        var data = {};

        // প্রতিটা group থেকে selected value collect
        $('.variant-group').each(function(){
            var $active = $(this).find('.variant-btn.active').first();
            if ($active.length) {
                var attr = $active.data('attr');
                var val  = $active.data('value');
                if (attr && val) {
                    data[attr] = val;
                }
            }
        });

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            dataType: "JSON",
            success: function (res) {
                if (res.variation) {
                    $('#variation_id').val(res.variation.id);

                    // ✅ Server-provided formatted price
                    if (res.display_price) {
                        $('.product_price').text(res.display_price);
                    }
                    if (res.display_old_price) {
                        $('.product_old_price').text(res.display_old_price).show();
                    } else {
                        $('.product_old_price').hide();
                    }

                    var vId = String(res.variation.id);
                    var vIds = (window.cartState && window.cartState.variationIds || []).map(String);
                    var $btn = $('#cart_form .product-cart-btn');

                    if (vIds.includes(vId)) {
                        $btn.find('.cart-btn-label').text($btn.data('in-cart-text') || 'Update Cart');
                    } else {
                        $btn.find('.cart-btn-label').text($btn.data('add-text') || 'Add to Cart');
                    }
                } else {
                    $('#variation_id').val('');
                }
                checkVariation();
            }
        });
    }

    $(document).ready(function () {
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
                { breakpoint: 1024, settings: { slidesToShow: 4 } },
                { breakpoint: 768,  settings: { slidesToShow: 3 } },
                { breakpoint: 480,  settings: { slidesToShow: 2 } }
            ]
        });

        $(window).on('resize', function () {
            $('.slider-for').slick('resize');
            $('.slider-nav').slick('resize');
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('[data-tab]');
        const panes = document.querySelectorAll('.tab-pane');

        tabs.forEach(tab => {
            tab.addEventListener('click', function () {
                tabs.forEach(t => t.classList.remove('bg-gray-200', 'focus:bg-gray-300'));
                this.classList.add('bg-gray-200', 'focus:bg-gray-300');

                panes.forEach(pane => pane.classList.add('hidden'));
                const tabContent = document.querySelector(`.tab-pane[data-tab-content="${this.getAttribute('data-tab')}"]`);
                tabContent.classList.remove('hidden');
            });
        });

        tabs[0].click();
    });

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