<!DOCTYPE html>
<html lang="en">

@include('partials.head')

<body class="min-h-screen flex flex-col">
    <!-- Header -->
    @include('partials.header')

    @yield('content')

    <!-- Footer Section -->
    @include('partials.footer')
    
    <!-- Shopping Cart Modal -->
    <div id="cartModal"
        class="fixed inset-y-0 right-0 bg-white w-full max-w-sm sm:max-w-md transform translate-x-full transition-transform duration-300 ease-in-out z-50 shadow-lg hidden">
        @include('carts.cart_section')
    </div>
    <!-- Overlay for Background Dimming -->
    <div id="cartOverlay" class="fixed inset-0 bg-black bg-opacity-50 hidden z-40" onclick="closeCartModal()"></div>

    <!-- Slick Slider JS -->
    @include('partials.js')
</body>

</html>