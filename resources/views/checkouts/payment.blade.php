@extends('layouts.app')
@section('content')
<div class="container mx-auto p-8 md:px-16">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Payment Methods -->
        
        <div class="lg:col-span-2 space-y-6">
            <!-- Success Message -->
            <div id="order-success" class="flex items-start p-4 mb-4 text-green-800 border border-green-300 rounded-lg bg-green-50 shadow-md" role="alert">
                <svg class="flex-shrink-0 w-6 h-6 text-green-600 mt-1" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.25 7.25a1 1 0 01-1.42 0l-3.25-3.25a1 1 0 111.42-1.42L8.75 11.1l6.54-6.54a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <div class="ms-3 text-sm">
                    <h3 class="text-lg font-semibold">{{ __('messages.order_placed_successfully') }}</h3>
                    <p class="mt-1">{{ __('messages.order_processing_message') }}</p>
                </div>
                <button type="button" onclick="document.getElementById('order-success').remove()" 
                        class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg p-1.5 hover:bg-green-100 focus:ring-2 focus:ring-green-400 inline-flex h-8 w-8">
                    <span class="sr-only">{{ __('messages.close') }}</span>
                    <i class="fa-solid fa-xmark flex-shrink-0 w-6 h-6 text-green-600 mt-1"></i>
                </button>
            </div>
        
            <!-- Payment Form -->
            <form method="post" action="{{ route('front.sellPaymentStore',[$sell->id])}}" id="checkout_form">
                @csrf
                <div class="shadow-sm border rounded-lg p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ __('messages.select_payment_method') }}</h2>
        
                    <!-- Payment Method Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                        
                        <!-- bKash -->
                        <!--<label class="border rounded-lg p-4 flex flex-col items-center justify-center h-24 cursor-pointer hover:border-gray-400 transition has-[:checked]:border-blue-500 has-[:checked]:bg-blue-100">-->
                        <!--    <input type="radio" name="payment_method" value="bkash" class="hidden">-->
                        <!--    <div class="w-20 h-8 flex items-center justify-center mb-2">-->
                        <!--        <span class="text-lg font-bold text-pink-600">{{ __('messages.bkash') }}</span>-->
                        <!--        <svg class="h-4 w-4 text-pink-600 ml-1" viewBox="0 0 24 24" fill="currentColor">-->
                        <!--            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />-->
                        <!--        </svg>-->
                        <!--    </div>-->
                        <!--    <p class="text-sm font-medium text-center">{{ __('messages.bkash') }}</p>-->
                        <!--</label>-->
                    
                        <!-- Cash on Delivery (Default Selected) -->
                        <label class="border-2 rounded-lg p-4 flex flex-col items-center justify-center h-24 cursor-pointer bg-blue-100 border-blue-500">
                            <input type="radio" name="payment_method" value="cash_on_delivery" class="hidden" checked>
                            <div class="w-20 h-8 bg-gray-100 flex items-center justify-center mb-2 rounded">
                                <span class="text-xs text-gray-600">{{ __('messages.paygear') }}</span>
                            </div>
                            <p class="text-sm font-medium text-center">{{ __('messages.cash_on_delivery') }}</p>
                        </label>
                    </div>
                    
                    <div class="error_payment_method"></div>
        
                    <!-- Pay Now Button -->
                    <div class="flex justify-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-3 rounded-md text-center w-full sm:w-48">
                            {{ __('messages.pay_now') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Column - Order Summary -->
        <div class="lg:col-span-1">
            <div class="shadow-sm border rounded-lg p-6">
                <!-- Order Summary -->
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">{{ __('messages.order_summary') }}</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <p class="text-gray-600">{{ __('messages.items_total') }} ({{ $sell->lines->count()}} {{ __('messages.items_and_shipping_fee_included') }})</p>
                            <p class="text-gray-800 font-medium">Tk {{ $sell->final_amount}}</p>
                        </div>
                        <div class="border-t border-gray-200 my-3 pt-3"></div>
                        <div class="flex justify-between">
                            <p class="text-gray-800 font-medium">{{ __('messages.total') }}:</p>
                            <p class="font-bold text-xl">Tk {{ $sell->final_amount}}</p>
                        </div>
                        <p class="text-gray-500 text-sm text-right">{{ __('messages.vat_included') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>

// setTimeout(() => {
//   document.getElementById('order-success')?.remove();
// }, 5000);
document.querySelectorAll('input[name="payment_method"]').forEach(function(radio) {
    radio.addEventListener('change', function() {

        // Remove selected design from all labels
        document.querySelectorAll('label').forEach(function(label) {
            label.classList.remove('border-blue-500', 'bg-blue-100', 'border-2');
        });

        // Add selected design to clicked label
        let parentLabel = this.closest('label');
        parentLabel.classList.add('border-blue-500', 'bg-blue-100', 'border-2');
    });
});

</script>
@endpush