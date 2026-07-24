<header class="bg-gray-900 text-white py-3 px-4">
        <div class="container mx-auto flex flex-col md:flex-row items-center justify-between">
            <!-- Logo Section -->
            <a href="{{ route('front.home')}}" class="pb-4">
                <div class="flex justify-center items-center">
                   @if($info && $info->title)
                        <h1 class="text-3xl md:text-4xl font-semibold tracking-wide">
                            {{ $info->title }}
                        </h1>
                    @endif
                </div>
            </a>

            <!-- Search Bar Section (Hidden on mobile) -->
            <div class="hidden md:flex w-full md:w-1/2 lg:w-2/5 mb-3 md:mb-0">
                <form action="{{ route('front.products.index')}}" class="flex w-full" >
                    <div class="relative flex-grow">
                        <input type="text" name="search" id="autoInput"
                            class="w-full py-2 px-4 pr-10 rounded-l text-gray-800 focus:outline-none text-sm md:text-base">
                        <button type="submit"
                            class="absolute right-0 top-0 h-full px-3 bg-white rounded-r text-gray-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <button type="submit"
                        class="bg-blue-500 text-white px-3 py-2 ml-2 rounded hover:bg-blue-600 transition text-sm md:text-base">
                        {{ __('messages.search') }}
                    </button>
                </form>
            </div>
            


            <!-- Account and Cart Section -->
            <div class="flex items-center space-x-3 md:space-x-4">
                <!-- Sidebar Toggle Button -->
                <div class="">
                    <button id="showCategoryBtn"
                        class="md:w-10 md:h-10 w-8 h-8 z-[1100] bg-white shadow text-gray-700 flex items-center justify-center hidden">
                        <i class="fas fa-bars text-base"></i>
                    </button>
                </div>

                <!-- Mobile Search Icon -->
                <div class="md:hidden">
                    <button id="mobile-search-toggle" class="text-white text-xl">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                

                <!-- Account -->
                <div class="flex items-center">
                    <select name="lang" id="langSelect"
                        class="ml-2 mr-2 py-2 px-3 bg-white text-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm md:text-base">
                        <option value="en" {{ app()->getLocale() == 'en' ? 'selected' : '' }}>English</option>
                        <option value="bn" {{ app()->getLocale() == 'bn' ? 'selected' : '' }}>বাংলা</option>
                    </select>

                    {{---hide for Custom order placed form
                    @guest
                    <a href="{{ login_url()}}">
                        <div class="text-right mr-2">
                            <div class="text-sm font-medium">{{ __('messages.login') }}</div>
                        </div>
                    </a>
                    @else
                    <a href="{{ route('front.dashboards.index')}}">
                        <div class="text-right mr-2">
                            <div class="text-xs">{{ __('messages.hello') }}, {{ auth()->user()->name}}</div>
                            <div class="text-sm font-medium">{{ __('messages.account') }}</div>
                        </div>
                    </a>
                    @endguest
                    end -hide for Custom order placed form
                    --}}

                </div>

                <!-- Returns & Orders -->
                {{--hide for Custom order placed form
                <div class="flex items-center">
                    <a href="{{ route('front.user-orders.index')}}">
                        <div class="text-right mr-2">
                            <!--<div class="text-xs">{{ __('messages.returns') }}</div>-->
                            <div class="text-sm font-medium">{{ __('messages.orders') }}</div>
                        </div>
                    </a>
                </div>
                end-hide for Custom order placed form
                --}}

                <!-- Cart -->
                <div class="relative">
                    <i class="fas fa-shopping-cart text-xl md:text-2xl cursor-pointer" onclick="openCartModal()"></i>
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-4 h-4 md:w-5 md:h-5 flex items-center justify-center cart-count">{{ getTotalCart() }}</span>
                </div>
            </div>
        </div>
        <!-- Mobile Search Bar (Hidden by default) -->
        <div id="mobile-search-bar" class="container mx-auto pt-2 hidden">
            <form action="{{ route('front.products.index')}}" method="GET" class="flex w-full">
                <div class="relative flex-grow">
                    <input type="text" name="search" placeholder="Search here..."
                        class="w-full py-2 px-4 pr-10 rounded-l text-gray-800 focus:outline-none text-sm">
                    <button type="submit"
                        class="absolute right-0 top-0 h-full px-3 bg-white rounded-r text-gray-500">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
                <button type="submit"
                    class="bg-blue-500 text-white px-3 py-2 ml-2 rounded hover:bg-blue-600 transition text-sm">
                    {{ __('messages.search') }}
                </button>
            </form>
        </div>
    </header>
    
    <script>
        document.getElementById('langSelect').addEventListener('change', function () {
            window.location.href = '/lang/' + this.value;
        });
    </script>