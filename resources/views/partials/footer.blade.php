<footer class="">
    {{--
    <div class="bg-[#FFC936] py-6 md:py-10 text-center">
        <div class="container mx-auto px-4">
            <h1 class="text-xl md:text-2xl lg:text-3xl font-bold mb-4 md:mb-6">{{ __('messages.explore_recommendations') }}
            </h1>
            @guest
                <a href="{{ login_url()}}">
                    <button class="bg-gray-900 text-white py-2 md:py-3 px-6 md:px-8 rounded-md w-full max-w-xs mb-3 md:mb-4 text-sm md:text-base">
                        {{ __('messages.login') }}
                    </button>
                </a>
            @else
                <a href="{{ route('front.dashboards.index')}}">
                    <button class="bg-gray-900 text-white py-2 md:py-3 px-6 md:px-8 rounded-md w-full max-w-xs mb-3 md:mb-4 text-sm md:text-base">
                        {{ __('messages.account') }}
                    </button>
                </a>
            @endguest
            @guest
                <p class="text-sm md:text-base text-gray-800">{{ __('messages.new_here') }}?
                    <a href="{{ route('register')}}" class="text-blue-600 hover:underline">{{ __('messages.start_now') }}.</a>
                </p>
            @endguest
        </div>
    </div>
    --}}
    <div class="bg-gray-900 text-white pt-8 md:pt-12 pb-4">
        <div class="container mx-auto px-4">
            <!-- Footer Links Section -->
            <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-4 gap-6 md:gap-8 mb-8 md:mb-12">
                <!-- Explore Column -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.about') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                        <li><a href="{{ route('front.aboutUs')}}" class="hover:text-yellow-400">{{ __('messages.about_us') }}</a></li>
                        <li><a href="{{ route('front.contactUs')}}" class="hover:text-yellow-400">{{ __('messages.contact_us') }}</a></li>
                    </ul>
                </div>
    
                <!-- Terms & Conditions + promotions Column -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.policies') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                       <li><a href="{{ route('front.termCondition')}}" class="hover:text-yellow-400">{{ __('messages.terms_conditions') }}</a></li>
                       <li><a href="{{ route('front.refundPolicy')}}" class="hover:text-yellow-400">{{ __('messages.refund') }}</a></li>
                    </ul>
                </div>

                {{--hide for Custom order placed form
                <!-- Account Column -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.account') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                        <li><a href="{{ login_url()}}" class="hover:text-yellow-400">{{ __('messages.sign_in') }}</a></li>
                        <li><a href="{{ route('front.user-orders.index')}}" class="hover:text-yellow-400">{{ __('messages.orders') }}</a></li>
                        <li><a href="{{ route('front.user-orders.index')}}" class="hover:text-yellow-400">{{ __('messages.track_my_order') }}</a></li>
                    </ul>
                </div>
                --}}

                <!-- Customer Column -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.quick_links') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                        <li><a href="{{ route('front.products.promotions')}}" class="hover:text-yellow-400">{{ __('messages.promotions') }}</a></li>
                        <li><a href="{{ route('front.becomeSeller')}}" class="hover:text-yellow-400">{{ __('messages.become_seller') }}</a></li>
                        <li><a href="{{ route('front.paymentMethods')}}" class="hover:text-yellow-400">{{ __('messages.payment_methods') }}</a></li>
                    </ul>
                </div>
    
                <!-- Join as Seller Column -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.support') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                        <li><a href="{{ route('front.faq')}}" class="hover:text-yellow-400">{{ __('messages.faq') }}</a></li>
                        <li><a href="{{ route('front.supportCenter')}}" class="hover:text-yellow-400">{{ __('messages.support_center') }}</a></li>
                    </ul>
                </div>
            </div>
    
            <hr class="border-gray-800 mb-8 md:mb-12">
    
            <!-- Contact and Address Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-8 md:mb-12">
                <!-- Contact Us -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.contact_us') }}</h3>
                    <ul class="space-y-2 md:space-y-3 text-sm md:text-base">
                        @if($info && $info->email)
                        <li>Email: {{ $info->email }}</li>
                        @endif
                        @if($info && $info->phone)
                        <li>Phone: {{ $info->phone }}</li>
                        @endif
                        
                        @if($info && $info->address)
                        <li>Address: {{ $info->address }}</li>
                        @endif
                        
                        @if($info && $info->whats_app_no)
                        <li>{{ __('messages.whatsapp') }}: <a href="https://wa.me/{{$info->whats_app_no}}" class="text-blue-500 hover:underline">Message us</a></li>
                        @endif
                    </ul>
                </div>
    
                <!-- Warehouse 1 -->
                @foreach($locations as $location)
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ $location->name}}</h3>
                    <address class="not-italic text-sm md:text-base">
                        {{ $location->address}}
                    </address>
                    <a href="#" class="text-blue-500 hover:underline flex items-center mt-2 text-sm md:text-base">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                       {{ __('messages.view_map') }}
                    </a>
                </div>
                @endforeach
                <!-- Warehouse 2 -->
                <div class="hidden sm:block lg:hidden">
                    <!-- This warehouse is hidden on mobile and large screens, but visible on medium screens -->
                </div>
    
                <!-- Warehouse 2 (visible on mobile and large screens) -->
                <!--<div class="sm:hidden lg:block">-->
                <!--    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">Ware house</h3>-->
                <!--    <address class="not-italic text-sm md:text-base">-->
                <!--        Brightwell Tower, 7th Floor<br>-->
                <!--        143 Studio Lane<br>-->
                <!--        Aldridge District, London, UK-->
                <!--    </address>-->
                <!--    <a href="#" class="text-blue-500 hover:underline flex items-center mt-2 text-sm md:text-base">-->
                <!--        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 mr-1" fill="none"-->
                <!--            viewBox="0 0 24 24" stroke="currentColor">-->
                <!--            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"-->
                <!--                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />-->
                <!--        </svg>-->
                <!--        View Map-->
                <!--    </a>-->
                <!--</div>-->
    
                <!-- Social Media -->
                <div>
                    <h3 class="text-gray-400 text-sm md:text-base mb-3 md:mb-4">{{ __('messages.social') }}</h3>
                    <div class="flex flex-wrap gap-3 md:gap-4">
                        @if($info && $info->facebook_link)
                        <a href="{{$info->facebook_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-facebook-f text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                        
                        @if($info && $info->linkedin_link)
                        <a href="{{$info->linkedin_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-linkedin-in text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                        
                         @if($info && $info->instagram_link)
                        <a href="{{$info->instagram_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-instagram text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                         @if($info && $info->pinterest_link)
                        <a href="{{$info->pinterest_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-pinterest-p text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                         @if($info && $info->youtube_link)
                        <a href="{{$info->youtube_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-youtube text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                         @if($info && $info->tiktok_link)
                        <a href="{{$info->tiktok_link}}"
                            class="bg-gray-800 hover:bg-gray-700 w-10 h-10 flex items-center justify-center rounded-full">
                            <i class="fab fa-tiktok text-white text-sm md:text-base"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
    
            <!-- Bottom Footer -->
            <div
                class="border-t border-gray-800 pt-4 md:pt-6 flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <ul class="flex flex-wrap justify-center md:justify-start gap-2 md:gap-4 text-xs md:text-sm">
                        <li><a href="{{ route('front.supportCenter')}}" class="hover:text-yellow-400">{{ __('messages.support_center') }}</a></li>
                        <!--<li class="text-gray-600 hidden md:inline">|</li>-->
                        <!--<li><a href="#" class="hover:text-yellow-400">{{ __('messages.support_security') }}</a></li>-->
                        <li class="text-gray-600 hidden md:inline">|</li>
                        <li><a href="{{ route('front.privacyPolicy')}}" class="hover:text-yellow-400">{{ __('messages.privacy_policy') }}</a></li>
                        <li class="text-gray-600 hidden md:inline">|</li>
                        <li><a href="{{ route('front.faq')}}" class="hover:text-yellow-400">{{ __('messages.faq') }}</a></li>
                    </ul>
                </div>
                <div class="text-gray-500 text-xs md:text-sm text-center md:text-right">
                    {{ __('messages.copyright') }}
                </div>
            </div>
        </div>
    </div>
    <div class="fixed bottom-6 right-6 flex flex-col items-center space-y-3 z-50">
        <!-- Cart Button -->
        <a href="javascript:void(0);"
           onclick="openCartModal()"
           class="relative bg-gradient-to-r from-blue-500 to-blue-600 
                  w-10 h-10 sm:w-12 sm:h-12 md:w-14 md:h-14 
                  rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition transform">
            <i class="fas fa-shopping-cart text-white text-base sm:text-lg md:text-xl"></i>
            <span id="cart-count" class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] sm:text-xs w-4 h-4 sm:w-5 sm:h-5 flex items-center justify-center rounded-full cart-count">
                {{ getTotalCart() }}
            </span>
        </a>
    
        <!-- Scroll To Top Button -->
        <button onclick="scrollToTop()" id="scrollBtn"
            class="hidden bg-blue-500 
                   w-9 h-9 sm:w-10 sm:h-10 md:w-12 md:h-12 
                   rounded-md flex items-center justify-center shadow-md hover:bg-blue-700 transition">
            <i class="fas fa-chevron-up text-white text-sm sm:text-base md:text-lg"></i>
        </button>
    </div>
</footer>
<script>
    const scrollBtn = document.getElementById("scrollBtn");

    // Always hide on load
    scrollBtn.classList.add("hidden");

    // Scroll event listener
    window.addEventListener("scroll", function () {
        if ((document.body.scrollTop > 200 || document.documentElement.scrollTop > 200)) {
            scrollBtn.classList.remove("hidden");
            scrollBtn.classList.add("flex");
        } else {
            scrollBtn.classList.add("hidden");
            scrollBtn.classList.remove("flex");
        }
    });

    function scrollToTop() {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    document.addEventListener("DOMContentLoaded", function () {
        const images = document.querySelectorAll('img[loading="lazy"]');
    
        images.forEach(img => {
            img.classList.add('lazy-loading');
    
            if (img.complete) {
                img.classList.remove('lazy-loading');
                img.classList.add('lazy-loaded');
            } else {
                img.addEventListener('load', function () {
                    img.classList.remove('lazy-loading');
                    img.classList.add('lazy-loaded');
                });
            }
        });
    });
</script>
