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
            
                <!-- Right Side: Cart Totals & Payment (Direct Server Rendered) -->
                <div class="lg:col-span-6 space-y-4" id="checkout_data">
                    @include('checkouts.data')
                </div>
            </div>
            
        </div>
    </form>
</div>

<!-- Modal -->
<div id="addressModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
    <div class="bg-white p-6 rounded-xl shadow-xl max-w-md w-full mx-4">
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {

    // ১. Helper Function: Reload Cart Summary via AJAX (Zero Blink)
    function reloadCartSummary() {
        $.ajax({
            url: "{{ route('front.checkouts.index') }}",
            type: "GET",
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(htmlResponse) {
                $('#checkout_data').html(htmlResponse);
            },
            error: function() {
                showToast('কার্ট আপডেট করতে সমস্যা হয়েছে!', 'error');
            }
        });
    }

    // ২. UI-Friendly Toast Notification (Browser Alert-এর বদলে)
    function showToast(message, type = 'success') {
        let bgColor = type === 'success' ? 'bg-green-600' : 'bg-red-500';
        let toastHtml = `
            <div id="checkout_toast" class="fixed bottom-5 right-5 ${bgColor} text-white px-5 py-3 rounded-lg shadow-xl text-sm font-medium z-50 transition-all duration-300 transform translate-y-0">
                ${message}
            </div>
        `;
        
        $('#checkout_toast').remove();
        $('body').append(toastHtml);

        setTimeout(function() {
            $('#checkout_toast').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }

    // ৩. Delivery/Shipping Options Change Handler
    $(document).on('change', 'input.shipping_id, input.delivery_id', function(e) {
        let shipping_id = $("input.shipping_id:checked").val();
        let delivery_id = $("input.delivery_id:checked").val();

        $.ajax({
            url: "{{ route('front.storeSession') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                delivery_id: delivery_id,
                shipping_id: shipping_id,
            },
            success: function (response) {
                reloadCartSummary();
            }
        });
    });

    // ৪. Coupon Apply AJAX Handler (No Reload, Inline Message + Toast)
    $(document).on('click', '#coupon_apply', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Form Submission বন্ধ করবে

        let couponCode = $('#coupon_code').val();
        let targetUrl = $(this).data('href');

        if (!couponCode) {
            showToast('অনুগ্রহ করে কুপন কোডটি লিখুন', 'error');
            return false;
        }

        let $btn = $(this);
        let originalText = $btn.html();
        $btn.html('Checking...').prop('disabled', true);

        $.ajax({
            url: targetUrl,
            type: "GET",
            data: {
                _token: "{{ csrf_token() }}",
                code: couponCode
            },
            success: function(response) {
                $btn.html(originalText).prop('disabled', false);

                if (response.success) {
                    showToast(response.msg || 'কুপন সফলভাবে প্রয়োগ করা হয়েছে!', 'success');
                    reloadCartSummary();
                } else {
                    showToast(response.msg || 'অবৈধ বা মেয়াদোত্তীর্ণ কুপন কোড!', 'error');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
                $btn.html(originalText).prop('disabled', false);
                showToast('কুপন প্রয়োগে সমস্যা হয়েছে। আবার চেষ্টা করুন।', 'error');
            }
        });

        return false;
    });

});

// Modal Global Functions
function openModal() {
    const modal = document.getElementById('addressModal');
    if(modal) modal.classList.remove('hidden');
}

function closeModal() {
    const modal = document.getElementById('addressModal');
    if(modal) modal.classList.add('hidden');
}
</script>
@endpush