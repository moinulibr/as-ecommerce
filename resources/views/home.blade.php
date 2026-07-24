@extends('layouts.app')

@section('content')
<!-- Navigation -->
<!--@if($features->count())-->
<!--<nav class="hidden md:block border-b border-gray-200 bg-white">-->
<!--    <div class="container mx-auto px-4 overflow-x-auto">-->
<!--        <div class="flex items-center justify-center space-x-4 md:space-x-6 py-3 whitespace-nowrap">-->
<!--            <span class="text-gray-500 hidden md:inline text-sm md:text-base">{{ __('messages.explore_the_sanitary') }}</span>-->
            
<!--            @foreach($features as $feature)-->
<!--            <a href="{{ route('front.products.index',['feature_id'=>$feature->id])}}" class="text-blue-500 font-medium text-sm md:text-base">{{ $feature->name}}</a>-->
<!--            @endforeach-->
            
<!--        </div>-->
<!--    </div>-->
<!--</nav>-->
<!--@endif-->

<!-- Main Content -->
<div class="container mx-auto py-4 md:py-6">
    <div class="flex flex-col md:flex-row">
        
        <!-- Sidebar Toggle Button for Mobile -->
        <div class="md:hidden">
            <button id="showCategoryBtn"
                class="md:w-10 md:h-10 w-8 h-8 fixed top-2.5 left-2.5 z-[1100] bg-white shadow text-gray-700 flex items-center justify-center hidden">
                <i class="fas fa-bars text-base"></i>
            </button>
        </div>

        <!-- Sidebar Category -->
        <div id="categorySidebar" class="category-sidebar md:w-1/5 md:pr-4 mb-4 md:mb-0 hidden md:block">
            <div class="bg-white rounded shadow p-4 md:pl-1 md:pt-0">
                <div class="mb-4 flex justify-between items-center">
                    <h3 class="text-xl font-bold">{{ __('messages.category') }}</h3>
                    <button id="categoryBtn" class="text-gray-500 md:hidden">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <ul class="space-y-2">
                    @foreach($cats->where('is_menu',1) as $cat)
                    <li>
                        <a href="{{ route('front.products.index',['cat_id'=>$cat->id])}}" class="flex items-center space-x-3 hover:text-blue-500 transition">
                            <img src="{{ $cat->image_url}}"
                                alt="{{ $cat->name}}" class="w-6 h-6 md:w-8 md:h-8 rounded-full" loading="lazy" decoding="async">
                                <span class="text-sm font-bold">
                                    {{ $cat->name}}
                                    <!--@if($cat->bd_name)-->
                                    <!--    <span class="text-gray-400 text-sm font-normal ml-1">-->
                                    <!--        ({{ $cat->bd_name }})-->
                                    <!--    </span>-->
                                    <!--@endif-->
                                </span>
                            </a>
                    </li>
                    @endforeach
                    
                </ul>
                <div class="mt-4 pt-4 border-t flex items-center justify-between">
                   <a href="{{ route('front.faq')}}"
                       class="flex items-center text-gray-600 hover:text-blue-500 transition text-sm md:text-base">
                        <i class="fas fa-question-circle mr-2"></i>
                        <span>{{ __('messages.faq') }}</span>
                    </a>
                    
                    <a href="{{ route('front.supportCenter')}}"
                       class="flex items-center text-gray-600 hover:text-blue-500 transition text-sm md:text-base">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <span>{{ __('messages.complain') }}</span>
                    </a>

                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="md:w-4/5">
            <!-- Hero Banner Slider -->
            <div class="mb-6">
                <div class="home-banner-slider flex flex-wrap md:flex-nowrap gap-4 md:gap-5">
                    <div class="w-full md:w-2/3">
                        <div class="hero-banner-slider-container">
                            @foreach($sliders->where('type',1) as $slider)
                            <div class="slide rounded-lg overflow-hidden">
                                <img src="{{ $slider->image_url}}"
                                    alt="{{ $slider->title}}" class="w-full object-cover" loading="eager" fetchpriority="high">
                            </div>
                            @endforeach

                        </div>
                    </div>
                    <div class="hero-banner-right-slider w-full md:w-1/3 hidden md:block">
                        @foreach($sliders->where('type',2) as $slider)
                        <div class="slide">
                            <div class="banner-right-image">
                                <img src="{{ $slider->image_url}}"
                                    alt="{{ $slider->title}}" class="w-full object-cover rounded-lg" loading="eager" fetchpriority="high">
                            </div>
                        </div>
                        @endforeach

                    </div>
                </div>
            </div>

            <!-- Top Category Slider -->
            <div class="mb-8 md:mb-12">
                <div class="mb-4 flex items-center">
                    <h1 class="text-lg md:text-xl font-bold">{{ __('messages.top_category') }}</h1>
                </div>
                <div class="category-slider">
                    @foreach($cats->where('is_top',1) as $cat)
                        <div class="text-center relative">
                            <a href="{{ route('front.products.index',['cat_id'=>$cat->id])}}">
                                <div class="relative w-20 md:w-24 mx-auto">
                                    <!-- Circle Image Wrapper -->
                                    <div class="relative w-20 md:w-24 mx-auto">
                                        <div class="w-20 h-20 md:w-24 md:h-24 rounded-full border-2 border-black overflow-hidden">
                                            <img src="{{ $cat->image_url}}" alt="{{ $cat->name}}" class="w-full h-full object-cover" loading="lazy" decoding="async">
                                        </div>
                                        
                                        @if($cat->discount)
                                        <span class="absolute top-2 right-[-15px] -translate-x-1/4 -translate-y-1/4 bg-red-600 text-white text-[10px] font-semibold px-2 py-1 rounded-full shadow z-10">
                                            {{ number_format($cat->discount->amount, 0) }} {{ $cat->discount->discount_type == 'percentage' ? '%' : 'tk' }} off
                                        </span>
                                        @endif
                                    </div>
                                </div>
                    
                                <!-- Category Name -->
                                <p class="mt-2 pt-4 category-name">{{ Str::limit($cat->name, 12, '..') }}</p>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Trending Products -->
            @if($products->where('is_reco',1)->count() >0)
            <div class="mb-8 md:mb-12">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold">{{ __('messages.trending_products') }}</h3>
                    <a href="{{ route('front.products.index',['is_reco'=>1])}}"
                        class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                        <span>{{ __('messages.more_products') }}</span><i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach($products->where('is_reco',1)->take(8) as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden w-full sm:w-[calc(50%-0.5rem)] lg:w-[calc(25%-0.75rem)]">
                        @include('products.section',['sproduct'=>$product])
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Featured Products -->
            @if($products->where('is_feature',1)->count() >0)
            <div class="mb-8 md:mb-12">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold">{{ __('messages.featured_products') }}</h3>
                    <a href="{{ route('front.products.index',['is_feature'=>1])}}"
                        class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                        <span>{{ __('messages.more_products') }}</span><i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach($products->where('is_feature',1)->take(8) as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden w-full sm:w-[calc(50%-0.5rem)] lg:w-[calc(25%-0.75rem)]">
                        @include('products.section',['sproduct'=>$product])
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($sliders->where('type',3)->count() > 0)
                <div class="mb-8 md:mb-12 pt-3">
                    <div class="flex flex-wrap justify-center gap-4">
                        @foreach($sliders->where('type',3) as $slider)
                        <div class="w-full sm:w-80 md:w-96"> <!-- Fixed width for consistency -->
                            @if($slider->link)
                            <a href="{{ url($slider->link) }}">
                            @endif
                            <div class="relative overflow-hidden rounded-3xl h-60 md:h-72 bg-cover bg-center"
                                 style="background-image: url('{{ $slider->image_url }}')">
                                <div class="absolute inset-0 bg-black/40"></div>
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-white p-4 md:p-6 z-10">
                                    <h2 class="text-xl md:text-3xl font-bold text-center mb-2">{{ $slider->title }}</h2>
                                    <p class="text-sm md:text-lg text-center text-gray-200">{{ $slider->description }}</p>
                                </div>
                            </div>
                            @if($slider->link)
                            </a>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Category Products: Clean Faucets -->
            
            <div id="sliders-container">
                <div class="text-center py-12 text-gray-500 loading-spinner">
                    Loading categories...
                </div>
            </div>

            
            @if($followedProducts->count())
            <div class="mb-8 md:mb-12">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold">{{ __('messages.from_vendors_you_follow') }}</h3>
                </div>
                <div class="flex flex-wrap justify-center gap-4">
                    @foreach($followedProducts as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden w-full sm:w-[calc(50%-0.5rem)] lg:w-[calc(25%-0.75rem)]">
                        @include('products.section',['sproduct'=>$product])
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Brand Card -->
            <div class="mb-8 md:mb-12">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg md:text-xl font-bold">{{ __('messages.brands') }}</h3>
                    <a href="{{ route('front.allBrands')}}"
                        class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                        <span>{{ __('messages.more_brands') }}</span><i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 px-2 sm:px-4 py-4">
                    @foreach($brands as $brand)
                        <a 
                            href="{{ route('front.products.index', ['brand_id' => $brand->id]) }}"
                            class="block group"
                        >
                            <div class="relative h-full bg-white border border-gray-200 rounded-lg p-3 sm:p-4 
                                        flex flex-col sm:flex-row items-center sm:items-start gap-3 sm:gap-4 
                                        transition-all duration-200 hover:shadow-md hover:border-gray-300">
                                
                                <!-- Discount Badge -->
                                @if($brand->discount)
                                    <span class="absolute top-0 right-0 bg-red-600 text-white text-[9px] sm:text-[10px] font-semibold 
                                                 px-1.5 sm:px-2 py-0.5 sm:py-1 rounded-bl rounded-tr z-10">
                                        {{ number_format($brand->discount->amount, 0) }}
                                        {{ $brand->discount->discount_type == 'percentage' ? '%' : 'tk' }} off
                                    </span>
                                @endif
                
                                <!-- Logo -->
                                <div class="flex-shrink-0">
                                    <img 
                                        src="{{ $brand->image_url }}" 
                                        alt="{{ $brand->name }} Logo" 
                                        class="w-10 h-10 sm:w-11 sm:h-11 object-contain"
                                        loading="lazy"
                                    >
                                </div>
                
                                <!-- Text -->
                                <div class="text-center sm:text-left">
                                    <p class="font-semibold text-sm sm:text-base text-gray-900 group-hover:text-blue-600 transition-colors">
                                        {{ Str::limit($brand->name, 15, '..') }}
                                    </p>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-0.5">
                                        {{$brand->products->count()}} Products
                                    </p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>                
        </main>
    </div>
</div>
@endsection

@push('js')

<script>
    
    $(document).ready(function () {
        // 1. Fire AJAX request on page load
        
        $.ajax({
            url: "{{ route('front.category.sliders') }}",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    // 2. Inject HTML content and remove spinner
                    $('#sliders-container').html(response.html);
    
                    // 3. Initialize Slick Slider on the freshly injected DOM elements
                    initializeSliders();
                }
            },
            error: function (xhr, status, error) {
                console.error("Failed to load sliders:", error);
                $('#sliders-container').html('<p class="text-red-500 text-center">Failed to load categories.</p>');
            }
        });
    
        // Wrapped slider initialization inside a reusable function
        function initializeSliders() {
            $('.category-slider-wrapper').each(function () {
                const $wrapper = $(this);
                const $slider = $wrapper.find('.category-product-slider');
                const $prev = $wrapper.find('.category-products-slick-prev');
                const $next = $wrapper.find('.category-products-slick-next');
    
                $slider.slick({
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    infinite: true,
                    prevArrow: $prev,
                    nextArrow: $next,
                    autoplay: true,
                    autoplaySpeed: 3000,
                    responsive: [
                        {
                            breakpoint: 1280,
                            settings: { slidesToShow: 3 }
                        },
                        {
                            breakpoint: 1024,
                            settings: { slidesToShow: 2 }
                        },
                        {
                            breakpoint: 640,
                            settings: { slidesToShow: 1 }
                        }
                    ]
                });
            });
        }
    });
</script>
@endpush
