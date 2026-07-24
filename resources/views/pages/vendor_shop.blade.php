@extends('layouts.app')
@section('content')

    <!-- ========== SHOP COVER SECTION ========== -->
    <section class="relative pt-10 sm:pt-0">
        <div class="relative h-60 w-full overflow-visible sm:overflow-hidden">
            <!-- Image Section (hidden on small screens) -->
            <div class="image-section hidden sm:block">
                <img src="https://amaderhat.com/img/hero-section.jpg"
                     class="w-full h-full object-cover" />
    
                <div class="absolute inset-0 bg-black/30"></div>
            </div>
    
            <div class="absolute inset-x-0 top-28 flex justify-center">
                <div class="shop-info-card bg-white rounded-2xl shadow-xl p-4 sm:p-6 md:p-8 flex flex-col md:flex-row md:items-center gap-4 sm:gap-6 w-11/12 sm:w-10/12 md:w-4/5 -translate-y-1/2 z-20">
    
                    <!-- Shop Logo -->
                    <div class="flex-shrink-0">
                        <img src="{{ $shop->image ? 'https://amader-sanitary.amaderhat.com/users/'. $shop->image : asset('img/avatar.jpg') }}"
                             class="w-20 h-20 sm:w-24 sm:h-24 md:w-28 md:h-28 rounded-full border-4 border-white shadow-lg object-cover mx-auto md:mx-0"
                             alt="Shop Logo">
                    </div>
    
                    <!-- Shop Info -->
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900 truncate">
                            {{ $shop->shop_name }}
                        </h1>
    
                        <p class="text-gray-500 mt-1 sm:mt-2 text-xs sm:text-sm md:text-base truncate">
                            {{ $shop->address }}
                        </p>
    
                        <div class="flex flex-wrap justify-center md:justify-start gap-4 sm:gap-6 mt-3 sm:mt-4 text-xs sm:text-sm text-gray-600">
                            <div class="flex items-center gap-1">
                                ⭐ <span class="font-medium text-gray-700">{{ $averageRating }} ({{ number_format($totalReviews) }} Reviews)</span>
                            </div>
                            <div class="flex items-center gap-1">
                                🛍️ <span>{{ $products->total() }} Products</span>
                            </div>
                            <div class="flex items-center gap-1">
                                📅 <span>Joined {{ \Carbon\Carbon::parse($shop->created_at)->format('M Y') }}</span>
                            </div>
                        </div>
                    </div>
    
                    <!-- Follow Button -->
                    <div class="flex gap-2 sm:gap-3 justify-center md:justify-end mt-3 md:mt-0 flex-wrap">
                        <button id="followBtn"
                                data-id="{{ $shop->user_id }}"
                                class="px-4 sm:px-5 py-2 rounded-full bg-indigo-600 text-white text-sm sm:text-base">
                            {{ $isFollowing ? 'Unfollow' : 'Follow' }}
                        </button>
                        <div class="flex items-center gap-2 px-4 sm:px-6 py-2 bg-gray-100 text-gray-700 rounded-full text-sm sm:text-base font-medium shadow-sm">
                            <!-- Optional Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m10-4.13a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                    
                            <span id="followerCount" class="font-semibold text-indigo-600">
                                {{ number_format($followerCount) }}
                            </span>
                    
                            <span>Followers</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ========== MAIN CONTENT ========== -->
    <section class="max-w-7xl mx-auto px-4 mt-10 mb-20">
    
        <!-- ================= PRODUCTS SECTION ================= -->
        <div class="mb-20">
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
                                                {{ $cat->products->count() }}
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
                                    <input type="number" id="maxPrice" value="30000"
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
                <div class="md:w-9/12 p-4 md:pt-1 md:pl-8 md:pr-0" id="productContainerFullSection">
                    <h1 class="text-xl font-bold mb-4">{{ __('messages.all_products') }}</h1>
                    <div class="relative mb-4">
                        <input id="searchInput" type="text" class="search2 w-full p-2 border border-gray-300 rounded"
                            placeholder="{{ __('messages.search_text') }}">
                        <button class="absolute right-0 top-0 h-full px-4 bg-gray-200 rounded-r">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
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
                    <div id="productContainer">
                        @include('pages.vendor_shop_products')
                    </div>
                </div>
            </div>
        </div>
    
    
        <!-- ================= ABOUT SHOP SECTION ================= -->
        <div class="border-t pt-16">
            <div class="text-center">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">
                    About {{ $shop->shop_name }}
                </h2>
            
                <p class="text-gray-600 text-base leading-relaxed mb-10 max-w-4xl mx-auto text-center">
                    Welcome to <span class="font-semibold">{{ $shop->shop_name }}</span>!
                    We focus on quality, reliability, and customer satisfaction.
                </p>
            </div>
    
            <div class="max-w-6xl mx-auto px-4">
        
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
                    <!-- Mission -->
                    <div class="bg-white border border-gray-100 p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300">
                        <h3 class="text-xl font-bold text-indigo-600 mb-4 flex items-center gap-2">
                            <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                            Our Mission
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ $shop->our_mission }}
                        </p>
                    </div>
            
                    <!-- Vision -->
                    <div class="bg-white border border-gray-100 p-8 rounded-2xl shadow-sm hover:shadow-lg transition duration-300">
                        <h3 class="text-xl font-bold text-indigo-600 mb-4 flex items-center gap-2">
                            <span class="w-2 h-6 bg-indigo-600 rounded-full"></span>
                            Our Vision
                        </h3>
                        <p class="text-gray-600 leading-relaxed text-sm">
                            {{ $shop->our_vision }}
                        </p>
                    </div>
            
                    <!-- Contact Info (Full Width) -->
                    <div class="md:col-span-2 bg-gradient-to-r from-indigo-50 to-indigo-100 p-8 rounded-2xl shadow-sm hover:shadow-md transition duration-300">
                        <h3 class="text-xl font-bold text-indigo-700 mb-6">
                            Contact Information
                        </h3>
            
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm text-gray-700">
                            <div>
                                <p class="font-semibold text-gray-900">Address</p>
                                <p>{{ $shop->address }}</p>
                            </div>
            
                            <div>
                                <p class="font-semibold text-gray-900">Email</p>
                                <p>{{ $shop->email }}</p>
                            </div>
            
                            <div>
                                <p class="font-semibold text-gray-900">Phone</p>
                                <p>{{ $shop->phone }}</p>
                            </div>
            
                            <div>
                                <p class="font-semibold text-gray-900">Website</p>
                                <a href="{{ $shop->website }}" 
                                   target="_blank" 
                                   rel="noopener noreferrer"
                                   class="text-indigo-600 break-all hover:underline cursor-pointer">
                                    {{ $shop->website }}
                                </a>
    
                            </div>
                        </div>
                    </div>
            
                </div>
            
            </div>
    
    
        </div>
    
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        
            // ============================
            // Mobile Category Sidebar
            // ============================
            const categoriesSection = document.getElementById('categoriesSection');
            const closeCategoryBtn = document.getElementById('closeCategoryBtn');
            let showCategoryBtn = document.getElementById('showCategoryBtn');
            
            // Dynamically create show button if missing
            if (!showCategoryBtn && window.innerWidth < 768 && categoriesSection) {
                showCategoryBtn = document.createElement('button');
                showCategoryBtn.id = 'showCategoryBtn';
                showCategoryBtn.className = 'fixed top-4 left-4 z-50 p-2 bg-indigo-600 text-white rounded-md';
                showCategoryBtn.innerHTML = '<i class="fas fa-bars"></i>';
                document.body.appendChild(showCategoryBtn);
            }
            
            function showCategoriesSidebar() {
                if (categoriesSection) {
                    categoriesSection.classList.remove('-translate-x-full', 'hidden');
                }
            }
            
            function hideCategoriesSidebar() {
                if (categoriesSection && !categoriesSection.classList.contains('-translate-x-full')) {
                    categoriesSection.classList.add('-translate-x-full');
                    setTimeout(() => categoriesSection.classList.add('hidden'), 300);
                }
            }
            
            showCategoryBtn?.addEventListener('click', function(e){
                e.stopPropagation(); // Prevent triggering the document click
                showCategoriesSidebar();
            });
            
            closeCategoryBtn?.addEventListener('click', hideCategoriesSidebar);
            
            // Close when clicking outside only if sidebar is visible
            document.addEventListener('click', function(e) {
                if (window.innerWidth < 768 &&
                    categoriesSection &&
                    !categoriesSection.classList.contains('hidden') &&
                    !categoriesSection.contains(e.target) &&
                    e.target !== showCategoryBtn) {
                    hideCategoriesSidebar();
                }
            });
        
        
            // ============================
            // Helper: Get checked values
            // ============================
            function getSelectedValues(className) {
                return Array.from(document.querySelectorAll('.' + className + ':checked')).map(el => {
                    return { id: el.value, name: el.dataset.name, bn: el.dataset.bn };
                });
            }
        
            // ============================
            // Update selected chips
            // ============================
            function updateSelectedChips() {
                const selectedCats = getSelectedValues('category');
                const selectedSubcats = getSelectedValues('subcategory');
                const container = document.getElementById('selectedCats');
                const section = document.getElementById('selectedCategorySection');
        
                container.innerHTML = '';
        
                const allSelected = [...selectedCats, ...selectedSubcats];
        
                if(allSelected.length === 0){
                    section.classList.add('hidden');
                    return;
                }
        
                section.classList.remove('hidden');
        
                allSelected.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center bg-blue-100 text-blue-600 px-2 py-1 rounded text-sm';
                    div.innerHTML = `
                        ${item.name} ${item.bn ? '('+item.bn+')' : ''}
                        <span data-id="${item.id}" class="removeCat ml-2 cursor-pointer text-red-500 font-bold">×</span>
                    `;
                    container.appendChild(div);
                });
        
                // Remove click action
                container.querySelectorAll('.removeCat').forEach(span => {
                    span.addEventListener('click', function() {
                        const id = this.dataset.id;
                        document.querySelectorAll(`.category[value="${id}"], .subcategory[value="${id}"]`).forEach(el => el.checked = false);
                        updateSelectedChips();
                        loadProducts();
                    });
                });
            }
        
            // ============================
            // Load products via AJAX
            // ============================
            function loadProducts(pageUrl = null){
                // Prevent [object Event] issue
                if(pageUrl instanceof Event) pageUrl = null;
        
                const search      = document.getElementById('searchInput')?.value || '';
                const sort        = document.querySelector('.shorting')?.value || '';
                const brand       = document.querySelector('.brands')?.value || '';
                const minPrice    = document.getElementById('minPrice')?.value || '';
                const maxPrice    = document.getElementById('maxPrice')?.value || '';
                const categories  = getSelectedValues('category').map(c=>c.id);
                const subcats     = getSelectedValues('subcategory').map(c=>c.id);
        
                const params = new URLSearchParams();
        
                if(search) params.append('q', search);
                if(sort) params.append('shorting', sort);
                if(brand) params.append('brand_id', brand);
                if(minPrice) params.append('min_price', minPrice);
                if(maxPrice) params.append('max_price', maxPrice);
        
                categories.forEach(c => params.append('category_id[]', c));
                subcats.forEach(c => params.append('sub_cat_id[]', c));
        
                const baseUrl = "{{ route('front.shop', $shop->slug) }}";
                const url = pageUrl ? pageUrl : `${baseUrl}?${params.toString()}`;
        
                fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } })
                    .then(res => res.text())
                    .then(data => {
                        document.getElementById('productContainer').innerHTML = data;
                        document.getElementById('productContainerFullSection').scrollIntoView({ behavior: 'smooth', block: 'start' });
                    })
                    .catch(err => console.error(err));
            }
        
            // ============================
            // Bind events
            // ============================
            function bindEvents() {
                // Search (debounce)
                let typingTimer;
                const searchInput = document.getElementById('searchInput');
                if(searchInput){
                    searchInput.addEventListener('keyup', function(){
                        clearTimeout(typingTimer);
                        typingTimer = setTimeout(loadProducts, 400);
                    });
                }
        
                // Sorting
                document.querySelector('.shorting')?.addEventListener('change', loadProducts);
        
                // Brand
                document.querySelector('.brands')?.addEventListener('change', loadProducts);
        
                // Category
                document.querySelectorAll('.category').forEach(el => el.addEventListener('change', function(){
                    updateSelectedChips();
                    loadProducts();
                }));
        
                // Subcategory
                document.querySelectorAll('.subcategory').forEach(el => el.addEventListener('change', function(){
                    updateSelectedChips();
                    loadProducts();
                }));
        
                // Price inputs
                document.getElementById('minPrice')?.addEventListener('change', loadProducts);
                document.getElementById('maxPrice')?.addEventListener('change', loadProducts);
        
                // Pagination links
                document.addEventListener('click', function(e){
                    const link = e.target.closest('.pagination a');
                    if(link){
                        e.preventDefault();
                        loadProducts(link.href);
                    }
                });
        
                // Follow/Unfollow button
                document.getElementById('followBtn')?.addEventListener('click', function(){
                    const button = this;
                    const vendorId = button.dataset.id;
        
                    fetch(`/vendor/follow/${vendorId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if(res.status === 401) { window.location.href = "/login"; return; }
                        return res.json();
                    })
                    .then(data => {
                        if(!data) return;
                        button.innerText = data.status === 'followed' ? 'Unfollow' : 'Follow';
                        document.getElementById('followerCount').innerText = data.count;
                    })
                    .catch(err => console.error(err));
                });
            }
        
            // ============================
            // Init
            // ============================
            updateSelectedChips();
            bindEvents();
            loadProducts();
        
        });
    </script>

@endsection
