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
                showCategoryBtn.classList.remove('hidden');
                categoryBtn.classList.add('hidden');
                categorySidebar.classList.add('hidden');
            } else { // Tablet and desktop view
                categorySidebar.classList.remove('hidden');
            }
        }
        
        handleResponsive();
        window.addEventListener('resize', handleResponsive);
        
        // Add event listener for mobile search toggle
        const mobileSearchToggle = document.getElementById('mobile-search-toggle');
        const mobileSearchBar = document.getElementById('mobile-search-bar');
        
        if (mobileSearchToggle && mobileSearchBar) {
            mobileSearchToggle.addEventListener('click', function() {
                mobileSearchBar.classList.toggle('hidden');
            });
        }
    });
    
    // Mobile menu and sidebar toggle
    const catBtn = document.getElementById('categoryBtn');
    if (catBtn) {
        catBtn.addEventListener('click', function () {
            const sidebar = document.getElementById('categorySidebar');
            const categoryBtn = document.getElementById('categoryBtn');
            const showCategoryBtn = document.getElementById('showCategoryBtn');

            sidebar.classList.add('hidden');
            sidebar.classList.remove('open');
            categoryBtn.classList.add('hidden');
            showCategoryBtn.classList.remove('hidden');
        });
    }

    // Show sidebar when clicking the showCategoryBtn
    const showCatBtn = document.getElementById('showCategoryBtn');
    if (showCatBtn) {
        showCatBtn.addEventListener('click', function () {
            const sidebar = document.getElementById('categorySidebar');
            const categoryBtn = document.getElementById('categoryBtn');
            const showCategoryBtn = document.getElementById('showCategoryBtn');

            sidebar.classList.remove('hidden');
            sidebar.classList.add('open');
            categoryBtn.classList.remove('hidden');
            showCategoryBtn.classList.add('hidden');
        });
    }

    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function (event) {
        const sidebar = document.getElementById('categorySidebar');
        const categoryBtn = document.getElementById('categoryBtn');
        const showCategoryBtn = document.getElementById('showCategoryBtn');

        if (
            sidebar && categoryBtn && showCategoryBtn &&
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
        // main hero slider
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
                    breakpoint: 1280,
                    settings: { slidesToShow: 6, slidesToScroll: 1 }
                },
                {
                    breakpoint: 1024,
                    settings: { slidesToShow: 5, slidesToScroll: 1 }
                },
                {
                    breakpoint: 640,
                    settings: { slidesToShow: 4, slidesToScroll: 1 }
                },
                {
                    breakpoint: 480,
                    settings: { slidesToShow: 3, slidesToScroll: 1 }
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

function closeCartModal() {
    const modal = document.getElementById('cartModal');
    const overlay = document.getElementById('cartOverlay');
    modal.classList.add('translate-x-full');
    setTimeout(() => {
        modal.classList.add('hidden');
        overlay.classList.add('hidden');
    }, 300);
}

function updateQuantity(itemId, change) {
    const quantityInput = document.getElementById(`quantity-${itemId}`);
    let quantity = parseInt(quantityInput.value) + change;
    if (quantity < 1) quantity = 1;
    quantityInput.value = quantity;
    updateCartTotal();
}

function removeItem(itemId) {
    const itemElement = document.getElementById(`quantity-${itemId}`).closest('.flex');
    itemElement.remove();
    updateCartTotal();
    checkCartEmpty();
}

function checkCartEmpty() {
    const cartItems = document.getElementById('cartItems');
    const emptyMessage = document.getElementById('emptyCartMessage');
    if (cartItems.querySelectorAll('.flex').length === 0) {
        emptyMessage.classList.remove('hidden');
    }
}

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

const cartIcon = document.querySelector('.fa-shopping-cart');
if (cartIcon && cartIcon.parentElement) {
    cartIcon.parentElement.addEventListener('click', openCartModal);
}
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

{{-- ============================================================ --}}
{{-- Cart State Sync — "Already in cart" button refresh            --}}
{{-- ============================================================ --}}
<script>
    // Fallback: layout-এ window.cartState না inject হলে
    window.cartState = window.cartState || { variationIds: [], productIds: [] };

    // Class definitions — single source of truth
    var CART_BTN_DARK_CLASSES  = 'bg-gray-900 hover:bg-gray-800 text-white';
    var CART_BTN_GREEN_CLASSES = 'bg-green-600 text-white cursor-not-allowed opacity-90 pointer-events-none';

    /**
     * Cart state অনুযায়ী সব product card-এর button update করে।
     * - List/Home card: disabled + color + text
     * - Detail page (label wrapper): শুধু text (কখনো disable না)
     */
    function refreshProductCartButtons() {
        if (!window.cartState) return;

        var vIds = (window.cartState.variationIds || []).map(String);
        var pIds = (window.cartState.productIds || []).map(String);

        $('.product-cart-btn').each(function () {
            var $btn = $(this);
            var vId  = $btn.attr('data-variation-id') ? String($btn.attr('data-variation-id')) : '';
            var pId  = $btn.attr('data-product-id')   ? String($btn.attr('data-product-id'))   : '';

            var inCart = (vId && vIds.indexOf(vId) !== -1) ||
                         (pId && pIds.indexOf(pId) !== -1);

            var $label = $btn.find('.cart-btn-label');
            var isDetailPage = $label.length > 0;

            // --- Detail page: শুধু label update, disable নয় ---
            if (isDetailPage) {
                if (inCart) {
                    $label.text($btn.data('in-cart-text') || 'Update Cart');
                } else {
                    $label.text($btn.data('add-text') || 'Add to Cart');
                }
                return; // next button
            }

            // --- List / Home card ---
            if (inCart) {
                $btn.removeClass(CART_BTN_DARK_CLASSES)
                    .addClass(CART_BTN_GREEN_CLASSES)
                    .prop('disabled', true)
                    .text($btn.data('in-cart-text') || 'Already in cart');

                // <a> হলে href neutralise করো
                if ($btn.is('a')) {
                    if (!$btn.data('original-href')) {
                        $btn.data('original-href', $btn.attr('href'));
                    }
                    $btn.attr('href', 'javascript:void(0)');
                }
            } else {
                $btn.removeClass(CART_BTN_GREEN_CLASSES)
                    .addClass(CART_BTN_DARK_CLASSES)   // ✅ এখানে text-white explicitly আছে
                    .prop('disabled', false)
                    .text($btn.data('add-text') || 'Add to Cart');

                // <a> হলে original href ফিরিয়ে দাও
                if ($btn.is('a') && $btn.data('original-href')) {
                    $btn.attr('href', $btn.data('original-href'));
                }
            }
        });
    }

    /**
     * AJAX response থেকে cart state update করে button refresh করে।
     */
    function applyCartStateFromResponse(res) {
        if (!res) return;

        if (Array.isArray(res.cart_variation_ids) || Array.isArray(res.cart_product_ids)) {
            window.cartState = {
                variationIds: (res.cart_variation_ids || []).map(String),
                productIds:   (res.cart_product_ids   || []).map(String)
            };
            refreshProductCartButtons();
        }
    }

    // Page load-এ একবার sync
    $(document).ready(function () {
        refreshProductCartButtons();
    });
</script>

@stack('js')