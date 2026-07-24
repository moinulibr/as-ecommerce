{{--
@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <form method="post" action="{{ route('front.checkouts.store')}}" id="checkout_form">
        @csrf
    <div class="max-w-6xl mx-auto p-1 md:p-2 font-sans text-gray-800 bg-gray-50 min-h-screen">
        
        <!-- Main Checkout Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-2 items-start">
            
            <!-- Left Side: Billing Details -->
            <div class="lg:col-span-6 bg-white p-6 rounded-lg shadow-sm border border-gray-100">
                <h2 class="text-xl md:text-2xl font-bold mb-6 text-black">Billing Details</h2>
                
                <form class="space-y-5">
                    <!-- Name Field -->
                    <div>
                        <label class="block text-sm md:text-base font-semibold mb-2 text-gray-900">
                            আপনার নাম <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" class="w-full bg-gray-100 border border-transparent rounded-lg py-3 px-4 focus:bg-white focus:border-gray-300 focus:outline-none transition">
                        <div class="error-name mt-1"></div>
                    </div>
        
                    <!-- Phone Number Field -->
                    <div>
                        <label class="block text-sm md:text-base font-semibold mb-2 text-gray-900">
                            আপনার ফোন নাম্বার <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" name="mobile" class="w-full bg-gray-100 border border-transparent rounded-lg py-3 px-4 focus:bg-white focus:border-gray-300 focus:outline-none transition">
                        <div class="error-mobile mt-1"></div>
                    </div>
        
                    <!-- Address Field -->
                    <div>
                        <label class="block text-sm md:text-base font-semibold mb-2 text-gray-900">
                            সম্পূর্ণ ঠিকানা লিখুন <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address" class="w-full bg-gray-100 border border-transparent rounded-lg py-3 px-4 focus:bg-white focus:border-gray-300 focus:outline-none transition">
                        <div class="error-address mt-1"></div>
                    </div>
        
                    <!-- Email Field (Optional) -->
                    <div>
                        <label class="block text-sm md:text-base font-semibold mb-2 text-gray-900">
                            ইমেইল (অপশনাল)
                        </label>
                        <input type="email" name="email" class="w-full bg-gray-100 border border-transparent rounded-lg py-3 px-4 focus:bg-white focus:border-gray-300 focus:outline-none transition">
                        <div class="error-email mt-1"></div>
                        
                    </div>
        
                    <!-- Customer Note -->
                    <div>
                        <label class="block text-sm md:text-base font-semibold mb-2 text-gray-900">
                            কাস্টমার নোট
                        </label>
                        <textarea rows="4" name="note" class="w-full bg-gray-100 border border-transparent rounded-lg py-3 px-4 focus:bg-white focus:border-gray-300 focus:outline-none transition resize-none"></textarea>
                    </div>
                </form>
            </div>
        
            <!-- Right Side: Cart Totals & Payment -->
            <div class="lg:col-span-6 space-y-4" id="checkout_data">
                
                
        
            </div>
        </div>
        
    </div>
    </form>
</div>

<!-- Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    
</div>
@endsection

@push('js')
<script>
$(document).on('change', 'input.shipping_id, input.delivery_id', function() {


    let shipping_id = $("input.shipping_id:checked").val();
    let delivery_id = $("input.delivery_id:checked").val();


    $.ajax({
        url: "{{ route('front.storeSession') }}",   // route for storing session
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            delivery_id: delivery_id,
            shipping_id: shipping_id,
        },
        success: function (response) {
            console.log("Saved in session:", response);
        }
    });
});
</script>

<script>
    
    function openModal() {
        const modal = document.getElementById('addressModal');
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        const modal = document.getElementById('addressModal');
        modal.classList.add('hidden');
    }
    
</script>



@endpush
--}}

@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4 md:p-6">
    <form method="post" action="{{ route('front.checkouts.store') }}" id="checkout_form">
        @csrf
        <div class="max-w-7xl mx-auto font-sans text-gray-800 bg-gray-50 min-h-screen rounded-xl p-2 md:p-4">
            
            <!-- Main Checkout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Side: Billing Details -->
                <div class="lg:col-span-6 bg-white p-6 md:p-8 rounded-xl shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 font-bold">
                            1
                        </div>
                        <div>
                            <h2 class="text-xl md:text-2xl font-bold text-gray-900">Billing Details</h2>
                            <p class="text-xs text-gray-500">পণ্য ডেলিভারির জন্য আপনার প্রয়োজনীয় তথ্য দিন</p>
                        </div>
                    </div>
                    
                    <div class="space-y-5">
                        <!-- Name Field -->
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-900">
                                আপনার নাম <span class="text-xs text-gray-400 font-normal">(Full Name)</span> <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" placeholder="যেমন: মোঃ মোইনুল ইসলাম" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                            <div class="error-name mt-1 text-xs text-red-500"></div>
                        </div>
            
                        <!-- Phone Number Field -->
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-900">
                                আপনার ফোন নাম্বার <span class="text-xs text-gray-400 font-normal">(Mobile Number)</span> <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" name="mobile" placeholder="যেমন: 01700000000" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                            <div class="error-mobile mt-1 text-xs text-red-500"></div>
                        </div>
            
                        <!-- Address Field -->
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-900">
                                সম্পূর্ণ ঠিকানা লিখুন <span class="text-xs text-gray-400 font-normal">(Full Address)</span> <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address" placeholder="বাসা/হোল্ডিং নং, রোড নং, এলাকা, থানা ও জেলা" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                            <div class="error-address mt-1 text-xs text-red-500"></div>
                        </div>
            
                        <!-- Email Field (Optional) -->
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-900">
                                ইমেইল <span class="text-xs text-gray-400 font-normal">(Email - Optional)</span>
                            </label>
                            <input type="email" name="email" placeholder="example@gmail.com" 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition">
                            <div class="error-email mt-1 text-xs text-red-500"></div>
                        </div>
            
                        <!-- Customer Note -->
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-900">
                                কাস্টমার নোট <span class="text-xs text-gray-400 font-normal">(Order Note - Optional)</span>
                            </label>
                            <textarea rows="3" name="note" placeholder="ডেলিভারি সংক্রান্ত বিশেষ কোনো নির্দেশ থাকলে লিখুন..." 
                                class="w-full bg-gray-50 border border-gray-200 rounded-lg py-3 px-4 text-sm text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 focus:outline-none transition resize-none"></textarea>
                        </div>
                    </div>
                </div>
            
                <!-- Right Side: Cart Totals & Payment (AJAX Container) -->
                <div class="lg:col-span-6 space-y-4" id="checkout_data">
                    @include('checkouts.data')
                    {{-- 
                       আপনার Partial/Cart Summary View-টি এখানে রেন্ডার হবে।
                       উপরে দেওয়া পূর্বের সংশোধিত Cart HTML টি এই সেকশনে ব্যবহার করবেন।
                    --}}
                </div>
            </div>
            
        </div>
    </form>
</div>

<!-- Address Modal -->
<div id="addressModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4">
        <!-- Modal Content Here -->
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {

    // 1. Delivery Options Change Handler (Session Storage via AJAX)
    $(document).on('change', 'input.shipping_id, input.delivery_id', function() {
        let shipping_id = $("input.shipping_id:checked").val();
        let delivery_id = $("input.delivery_id:checked").val();

        // Radio button data-price অনুযায়ী ফ্রন্টএন্ড চার্জ টেক্সট আপডেট
        let selectedPrice = $(this).data('price');
        if (selectedPrice !== undefined) {
            $('.charge').text(selectedPrice);
            
            // Recalculate Subtotal + Delivery Fee
            let subtotal = parseFloat($('#checkoutTotalAmount').data('totalamount')) || 0;
            let finalTotal = subtotal + parseFloat(selectedPrice);
            $('.total_amount').text(finalTotal.toFixed(2));
        }

        $.ajax({
            url: "{{ route('front.storeSession') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                delivery_id: delivery_id,
                shipping_id: shipping_id,
            },
            success: function (response) {
                console.log("Saved in session:", response);
            },
            error: function (xhr) {
                console.error("Session update failed", xhr);
            }
        });
    });

    // 2. Coupon Apply AJAX Handler (Prevents Page Reload)
    $(document).on('click', '#coupon_apply', function(e) {
        e.preventDefault();
        
        let couponCode = $('#coupon_code').val();
        let targetUrl = $(this).data('href');

        if (!couponCode) {
            alert('কুপন কোড লিখুন');
            return;
        }

        let $btn = $(this);
        let originalText = $btn.html();
        $btn.html('...').prop('disabled', true);

        $.ajax({
            url: targetUrl,
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                code: couponCode
            },
            success: function(response) {
                $btn.html(originalText).prop('disabled', false);

                if (response.success) {
                    // কুপন সেভ হবার পর AJAX দিয়ে checkouts.index রুটে কল করে নতুন HTML আনবে
                    $.ajax({
                        url: "{{ route('front.checkouts.index') }}",
                        type: "GET",
                        success: function(dataResponse) {
                            if (dataResponse.html) {
                                $('#checkout_data').html(dataResponse.html);
                            }
                        }
                    });
                } else {
                    alert(response.msg || 'অবৈধ কুপন!');
                }
            },
            error: function() {
                $btn.html(originalText).prop('disabled', false);
                alert('একটি এরর হয়েছে!');
            }
        });
    });

});

// Modal Control Functions
function openModal() {
    const modal = document.getElementById('addressModal');
    if (modal) modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('addressModal');
    if (modal) modal.classList.add('hidden');
}
</script>
@endpush
