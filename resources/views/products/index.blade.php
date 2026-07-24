@extends('layouts.app')
@push('css')
<link rel="stylesheet" href="{{ asset('assets/css/pages/shop.css')}}">

<link href="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css" rel="stylesheet">
@endpush
@section('content')
<div class="container mx-auto md:py-6">
    <div class="flex flex-col md:flex-row">
        <!-- Categories Section (col-3) -->
        <div class="md:w-3/12">
            <div id="categoriesSection" class="fixed p-4 top-0 left-0 h-full w-3/4 bg-white border border-gray-200 z-[1000] overflow-y-auto transform -translate-x-full transition-transform duration-300 ease-in-out md:static md:transform-none md:w-auto md:shadow-none md:z-auto hidden md:block">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold">{{ __('messages.category') }}</h2>
                    <button id="closeCategoryBtn" class="text-gray-500 md:hidden">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="category-section">
                    <ul class="space-y-1 mb-4">
                        <!-- Accessories -->
                        @foreach($cats as $cat)
                            <li>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input 
                                                type="checkbox" 
                                                class="category w-4 h-4 rounded border-gray-300" 
                                                value="{{ $cat->id }}"
                                                data-name="{{ $cat->name }}"
                                                data-bn="{{ $cat->bd_name ?? '' }}"
                                                {{ request('cat_id') == $cat->id ? 'checked' : '' }}
                                            >

                                            <span class="text-blue-500 text-sm font-bold">
                                                {{ $cat->name }}
                                                @if($cat->bd_name)
                                                    <span class="text-gray-400 text-sm font-normal ml-1">
                                                        ({{ $cat->bd_name }})
                                                    </span>
                                                @endif
                                            </span>
                                        </label>
                                    </div>
                                    <span class="inline-block bg-gray-200 text-gray-800 rounded-full px-2 py-1 text-xs font-bold">
                                        {{ $cat->productwithprice->count() }}
                                    </span>
                                </div>
                            
                                @if($cat->subcats)
                                    <ul class="mt-2 space-y-2 pl-8">
                                        @foreach($cat->subcats as $subcat)
                                        <li class="flex items-center justify-between">
                                            <label class="flex items-center gap-2 cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    class="subcategory w-4 h-4 rounded border-gray-300" 
                                                    value="{{ $subcat->id }}"
                                                    data-name="{{ $subcat->name }}"
                                                    data-bn="{{ $subcat->bd_name ?? '' }}"
                                                >
                                                <span class="text-gray-800 text-sm font-medium">
                                                    {{ $subcat->name }}
                                                    @if($cat->bd_name)
                                                        <span class="text-gray-400 text-sm font-normal ml-1">
                                                            ({{ $cat->bd_name }})
                                                        </span>
                                                    @endif
                                                </span>
                                            </label>
                                            <span class="inline-block bg-gray-200 text-gray-800 rounded-full px-2 py-1 text-xs font-bold">94</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="mb-4">
                    <h2 class="font-bold mb-4"> {{ __('messages.brands') }} </h2>
                    <select class="w-full p-2 border border-gray-300 rounded mb-4 brands">

                        <option value="">{{ __('messages.select_brand') }}</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id}}" {{request('brand_id')== $brand->id ? 'selected':''}}>
                                {{ $brand->name }}
                                @if($brand->bd_name)
                                    ({{ $brand->bd_name }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <h2 class="font-bold mb-4">{{ __('messages.price') }}</h2>
                    <div class="mx-2">
                        <div id="priceSlider" class="mb-4"></div>
                    </div>
                    <div class="flex justify-between">
                        <div>
                            <span>{{ __('messages.min') }}</span>
                            <input type="number" id="minPrice" value="0"
                                class="w-20 p-1 border border-gray-300 rounded ml-2">
                        </div>
                        <div class="pl-1">
                            <span>{{ __('messages.max') }}</span>
                            <input type="number" id="maxPrice" value="100000"
                                class="w-20 p-1 border border-gray-300 rounded ml-2">
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <h2 class="font-bold mb-4">{{ __('messages.sort_by') }}</h2>
                    <select class="shorting w-full p-2 border border-gray-300 rounded">
                        <option value="desc">{{ __('messages.latest_products') }}</option>
                        <option value="asc">{{ __('messages.old_products') }}</option>
                        <option value="price_high">{{ __('messages.high_to_low_price') }}</option>
                        <option value="price_low">{{ __('messages.low_to_high_price') }}</option>
                        <option value="name">{{ __('messages.name_asc') }}</option>
                        <option value="name_desc">{{ __('messages.name_desc') }}</option>
                    </select>
                </div>
            </div>
        </div>
        <!-- Product Section (col-9) -->
        <div class="md:w-9/12 p-4 md:pt-1 md:pl-8 md:pr-0">
            <h1 class="text-xl font-bold mb-4">{{ __('messages.all_products') }}</h1>
            <!-- Search Bar -->
            <div class="relative mb-4">
                <input type="text" class="search2 w-full p-2 border border-gray-300 rounded"
                    placeholder="{{ __('messages.search_text') }}">
                <button class="absolute right-0 top-0 h-full px-4 bg-gray-200 rounded-r">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <!-- Category Filter -->
            <div id="selectedCategorySection" class="mb-4 flex items-center gap-3 flex-wrap">
                <span class="flex items-center gap-2 text-[16px] font-semibold text-[#373737]">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-[#0078d7] text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
            
                    Selected Category
                </span>
                <div id="selectedCats" class="flex flex-wrap gap-2"></div>
            </div>


            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" id="product_data">
                
                
            </div>
            <div id="scrollLoader" class="hidden w-full flex justify-center items-center py-8">
                <div class="flex items-center gap-3 bg-white shadow-md px-5 py-3 rounded-full border">
                    <div class="h-6 w-6 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
                    <span class="text-sm font-semibold text-gray-600">
                        Loading products...
                    </span>
                </div>
            </div>
        </div>
        
        <div id="loader" class="flex justify-center items-center py-8">
            <div class="h-10 w-10 animate-spin rounded-full border-4 border-gray-200 border-t-blue-600"></div>
        </div>

    </div>
    </div>
@endsection

@push('js')
<!-- In <head> or before </body> -->
<script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js"></script>

<script>

    let page = 1;
    let loading = false;
    let hasMoreData = true;
    
    // Initialize mobile view settings on page load
    document.addEventListener('DOMContentLoaded', function () {
        if (window.innerWidth < 768) {
            // For mobile view, show the toggle button
            const showCategoryBtn = document.getElementById('showCategoryBtn');
            if (showCategoryBtn) {
                showCategoryBtn.classList.remove('hidden');
            }

            // Hide the category button in mobile view
            const categoryBtn = document.getElementById('categoryBtn');
            if (categoryBtn) {
                categoryBtn.classList.add('hidden');
            }
        }

        // Add event listener for mobile search toggle
        const mobileSearchToggle = document.getElementById('mobile-search-toggle');
        const mobileSearchBar = document.getElementById('mobile-search-bar');

        if (mobileSearchToggle && mobileSearchBar) {
            mobileSearchToggle.addEventListener('click', function () {
                mobileSearchBar.classList.toggle('hidden');
            });
        }
    });

    const slider = document.getElementById('priceSlider');
    const minInput = document.getElementById('minPrice');
    const maxInput = document.getElementById('maxPrice');

    if (slider && minInput && maxInput) {
        noUiSlider.create(slider, {
            start: [0, 100000],
            connect: true,
            range: {
                'min': 0,
                'max': 100000
            },
            step: 100,
            tooltips: [true, true],
            format: {
                to: value => Math.round(value),
                from: value => parseInt(value)
            }
        });

        slider.noUiSlider.on('update', (values, handle) => {
            const [min, max] = values;
            minInput.value = min;
            maxInput.value = max;

        });

        slider.noUiSlider.on('change', function (values, handle) {
            reloadProducts();
        });


    }

    // Mobile category sidebar functionality
    document.addEventListener('DOMContentLoaded', function () {
        const categoriesSection = document.getElementById('categoriesSection');
        const toggleCategoryBtn = document.getElementById('toggleCategoryBtn');
        const closeCategoryBtn = document.getElementById('closeCategoryBtn');
        const showCategoryBtn = document.getElementById('showCategoryBtn');

        // Function to show the categories sidebar
        function showCategoriesSidebar() {
            if (categoriesSection) {
                categoriesSection.classList.remove('hidden');
                categoriesSection.classList.remove('-translate-x-full');
            }
        }

        // Function to hide the categories sidebar
        function hideCategoriesSidebar() {
            if (categoriesSection) {
                if (window.innerWidth < 768) {
                    categoriesSection.classList.add('-translate-x-full');
                    // Add a small delay before hiding to allow the animation to complete
                    setTimeout(function () {
                        categoriesSection.classList.add('hidden');
                    }, 300);
                }
            }
        }

        // Toggle button click event
        if (toggleCategoryBtn) {
            toggleCategoryBtn.addEventListener('click', function () {
                showCategoriesSidebar();
            });
        }

        // Show category button in header click event
        if (showCategoryBtn) {
            showCategoryBtn.addEventListener('click', function () {
                showCategoriesSidebar();
            });
        }

        // Close button click event
        if (closeCategoryBtn) {
            closeCategoryBtn.addEventListener('click', function () {
                hideCategoriesSidebar();
            });
        }

        // Close when clicking outside
        document.addEventListener('click', function (event) {
            if (categoriesSection &&
                !categoriesSection.contains(event.target) &&
                (toggleCategoryBtn && event.target !== toggleCategoryBtn && !toggleCategoryBtn.contains(event.target)) &&
                (showCategoryBtn && event.target !== showCategoryBtn && !showCategoryBtn.contains(event.target)) &&
                window.innerWidth < 768) {
                hideCategoriesSidebar();
            }
        });
    });


   function reloadProducts() {
        page = 1;
        hasMoreData = true;
        fetchData(page, false);
    }
    
    $('.category, .brands, .subcategory, .shorting').change(function () {
        updateSelectedCats();
        reloadProducts();
    });
    
    $('.removeCat').click(function () {
        reloadProducts();
    });

    let searchTimer;

    $('.search2').on('keyup', function () {
        clearTimeout(searchTimer);
    
        searchTimer = setTimeout(function () {
            reloadProducts();
        }, 500);
    });

    $(document).ready(function(){
        const urlParams = new URLSearchParams(window.location.search);
        const searchParam = urlParams.get('search');
        if(searchParam){
            $('input.search2').val(searchParam);
        }
    
        updateSelectedCats();
        fetchData(page, false);
    });
    

    function fetchData(page, append = false) {

        if (loading || !hasMoreData) {
            return;
        }
    
        loading = true;
    
        append
            ? $('#scrollLoader').removeClass('hidden')
            : $('#loader').removeClass('hidden');
    
        $.ajax({
            type: 'GET',
            url: '{{ route("front.products.index") }}',
            data: {
                page: page,
                max_price: $('#maxPrice').val(),
                min_price: $('#minPrice').val(),
                sub_category_id: $('.subcategory:checked').map(function () {
                    return $(this).val();
                }).get(),
                category_id: $('.category:checked').map(function () {
                    return $(this).val();
                }).get(),
                q: $('.search2').val(),
                shorting: $('.shorting').val(),
                brand_id: $('.brands').val()
            },
            success: function (res) {
    
                if (append) {
                    $('#product_data').append(res.html);
                } else {
                    $('#product_data').html(res.html);
                }
    
                hasMoreData = res.hasMore;
            },
            complete: function () {
                loading = false;
                $('#loader,#scrollLoader').addClass('hidden');
            }
        });
    }

   
    
    let scrollTimer;

    $(window).on('scroll', function () {
    
        if (!hasMoreData || loading) {
            return;
        }
    
        clearTimeout(scrollTimer);
    
        scrollTimer = setTimeout(function () {
    
            if (
                $(window).scrollTop() + $(window).height() >=
                $(document).height() - 700
            ) {
                page++;
                fetchData(page, true);
            }
    
        }, 100);
    
    });

   
    //Start Selected Cat show code
    function updateSelectedCats() {
        let selected = $('.category:checked').map(function () {
            return {
                id: $(this).val(),
                name: $(this).data('name'),
                bn: $(this).data('bn')
            };
        }).get();
    
        let container = $('#selectedCats');
        let section = $('#selectedCategorySection');
        
        container.html(''); // clear old items
    
        if (selected.length === 0) {
            section.addClass('hidden');
            return;
        }
        
        section.removeClass('hidden');
    
        selected.forEach(cat => {
            container.append(`
                <div class="flex items-center bg-blue-100 text-blue-600 px-2 py-1 rounded text-sm">
                    ${cat.name} ${cat.bn ? '('+cat.bn+')' : ''}
                    <span data-id="${cat.id}" class="removeCat ml-2 cursor-pointer text-red-500 font-bold">×</span>
                </div>
            `);
        });
    }
    
    $(document).on('click', '.removeCat', function () {
        let id = $(this).data('id');
        
        // Uncheck checkbox
        $('.category[value="'+id+'"]').prop('checked', false);
    
        updateSelectedCats();
    });
       
    //End Selected Cat show code
</script>

@endpush