<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script src="{{ asset('assets/js/cart.js')}}"></script>
<script src="{{ asset('assets/js/checkout.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

<script>
    // Initialize mobile view settings on page load
    document.addEventListener('DOMContentLoaded', function() {
        const categorySidebar = document.getElementById('categorySidebar');
        const showCategoryBtn = document.getElementById('showCategoryBtn');
        const categoryBtn = document.getElementById('categoryBtn');
        
        // Function to handle responsive behavior
        function handleResponsive() {
            if (window.innerWidth < 768) { // Mobile view
                // Show the toggle button
                showCategoryBtn.classList.remove('hidden');
                category-slider-wrapper
                // Hide the category button
                categoryBtn.classList.add('hidden');
                
                // Hide sidebar on mobile by default
                categorySidebar.classList.add('hidden');
            } else { // Tablet and desktop view
                // Always show sidebar on tablet/desktop
                categorySidebar.classList.remove('hidden');
            }
        }
        
        // Run on page load
        handleResponsive();
        
        // Run on window resize
        window.addEventListener('resize', handleResponsive);
        
        
        // Add event listener for mobile search toggle
        const mobileSearchToggle = document.getElementById('mobile-search-toggle');
        const mobileSearchBar = document.getElementById('mobile-search-bar');
        
        mobileSearchToggle.addEventListener('click', function() {
            mobileSearchBar.classList.toggle('hidden');
        });
    });
    
    // Mobile menu and sidebar toggle
    document.getElementById('categoryBtn').addEventListener('click', function () {
        const sidebar = document.getElementById('categorySidebar');
        const categoryBtn = document.getElementById('categoryBtn');
        const showCategoryBtn = document.getElementById('showCategoryBtn');

        sidebar.classList.add('hidden'); // Hide sidebar
        sidebar.classList.remove('open'); // Remove mobile open state
        categoryBtn.classList.add('hidden'); // Hide category button
        showCategoryBtn.classList.remove('hidden'); // Show X button
    });

    // Show sidebar when clicking the showCategoryBtn
    document.getElementById('showCategoryBtn').addEventListener('click', function () {
        const sidebar = document.getElementById('categorySidebar');
        const categoryBtn = document.getElementById('categoryBtn');
        const showCategoryBtn = document.getElementById('showCategoryBtn');

        sidebar.classList.remove('hidden'); // Show sidebar
        sidebar.classList.add('open'); // Add mobile open state
        categoryBtn.classList.remove('hidden'); // Show category button
        showCategoryBtn.classList.add('hidden'); // Hide X button
    });

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const sidebar = document.getElementById('categorySidebar');
        const categoryBtn = document.getElementById('categoryBtn');
        const showCategoryBtn = document.getElementById('showCategoryBtn');

        if (
            !sidebar.contains(event.target) &&
            event.target !== categoryBtn &&
            !categoryBtn.contains(event.target) &&
            event.target !== showCategoryBtn &&
            !showCategoryBtn.contains(event.target) &&
            window.innerWidth < 768
        ) {
            sidebar.classList.add('hidden');
            sidebar.classList.remove('open');
            categoryBtn.classList.add('hidden');
            showCategoryBtn.classList.remove('hidden');
        }
    });

    $(document).ready(function () {
        // main hero silder
        $('.hero-banner-slider-container').slick({
            dots: true,
            arrows: false,
            infinite: true,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ]
        });
        
        $('.hero-banner-right-slider').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1
                        }
                    }
                ]
            });

        
        
        
    });

    // Top category
    $(document).ready(function () {
        $('.category-slider').slick({
            infinite: true,
            slidesToShow: 8,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 3000,
            responsive: [
                {
                    breakpoint: 1280, // Large screens (laptops/desktops)
                    settings: {
                        slidesToShow: 6,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 1024, // Tablets
                    settings: {
                        slidesToShow: 5,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 640, // Small screens
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 1,
                    }
                },
                {
                    breakpoint: 480, // Mobile
                    settings: {
                        slidesToShow: 3,
                        slidesToScroll: 1,
                    }
                }
            ]
        });
    });
</script>

    <script>
    function openCartModal() {
        const modal = document.getElementById('cartModal');
        const overlay = document.getElementById('cartOverlay');
        modal.classList.remove('hidden', 'translate-x-full');
        overlay.classList.remove('hidden');
    }

    // Close Cart Modal
    function closeCartModal() {
        const modal = document.getElementById('cartModal');
        const overlay = document.getElementById('cartOverlay');
        modal.classList.add('translate-x-full');
        setTimeout(() => {
            modal.classList.add('hidden');
            overlay.classList.add('hidden');
        }, 300); // Match transition duration
    }

    // Update Quantity
    function updateQuantity(itemId, change) {
        const quantityInput = document.getElementById(`quantity-${itemId}`);
        let quantity = parseInt(quantityInput.value) + change;
        if (quantity < 1) quantity = 1;
        quantityInput.value = quantity;
        updateCartTotal();
    }

    // Remove Item
    function removeItem(itemId) {
        const itemElement = document.getElementById(`quantity-${itemId}`).closest('.flex');
        itemElement.remove();
        updateCartTotal();
        checkCartEmpty();
    }

    // Check if Cart is Empty
    function checkCartEmpty() {
        const cartItems = document.getElementById('cartItems');
        const emptyMessage = document.getElementById('emptyCartMessage');
        if (cartItems.querySelectorAll('.flex').length === 0) {
            emptyMessage.classList.remove('hidden');
        }
    }

    // Update Cart Total (Demo calculation)
    function updateCartTotal() {
        const items = document.querySelectorAll('#cartItems .flex');
        let total = 0;
        items.forEach(item => {
            const price = parseFloat(item.querySelector('.text-red-500').textContent.replace('Tk ', '').replace(',', ''));
            const quantity = parseInt(item.querySelector('input').value);
            total += price * quantity;
        });
        document.getElementById('cartTotal').textContent = `Tk ${total.toLocaleString()}`;
    }

    // Add event listener to cart icon in header
    document.querySelector('.fa-shopping-cart').parentElement.addEventListener('click', openCartModal);

    // Add event listener for mobile search toggle (from original script)
    const mobileSearchToggle = document.getElementById('mobile-search-toggle');
    const mobileSearchBar = document.getElementById('mobile-search-bar');
    mobileSearchToggle.addEventListener('click', function () {
        mobileSearchBar.classList.toggle('hidden');
    });
</script>


<script>
  var typedStrings = [
    "{{ __('messages.welcome') }}",
    "{{ __('messages.search_text') }}"
  ];

  var typed = new Typed('#autoInput', {
    strings: typedStrings,
    typeSpeed: 60,
    backSpeed: 30,
    backDelay: 1500,
    loop: true,
    attr: 'placeholder',
    bindInputFocusEvents: true
  });
</script>


@stack('js')