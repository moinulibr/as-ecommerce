@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-3xl w-full mx-auto rounded-lg p-6">
        <!-- Success Icon -->
        <div class="flex justify-center mb-4">
            <div class="bg-green-500 rounded-full p-4 w-16 h-16 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <!-- Thank You Message -->
        <div class="text-center mb-6">
            <h1 class="text-xl md:text-2xl font-medium text-gray-800">{{ __('messages.thank_you_purchase') }}</h1>
            <p class="text-2xl md:text-3xl font-bold mt-2 text-gray-900">{{ priceFormate($sell->final_amount) }}</p>
            <p class="text-gray-600 mt-2">{{ __('messages.order_number_is') }} {{ $sell->invoice_no }}</p>
        </div>

        <!-- Delivery Info -->
        <div class="mt-8 mb-4">
            <div class="flex justify-between m-1">
                <a href="{{ route('front.products.index') }}" class="bg-blue-500 text-white text-center p-2 rounded-md">
                    {{ __('messages.continue_shopping') }}
                </a>

                
            </div>

            <div class="border rounded-lg p-4">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                    <div class="flex items-center">
                        <img src="https://images.unsplash.com/photo-1584622650111-993a426fbf0a?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
                             alt="Product" class="w-16 h-16 object-cover rounded">
                        <div class="ml-4 hidden md:block">
                            <!-- Optional product details could go here -->
                        </div>
                    </div>
                    <div class="mt-3 md:mt-0">
                        <p class="text-gray-800 font-medium">{{ dateFormate($sell->transaction_date) }}</p>
                    </div>
                </div>

                <div class="border-t my-4"></div>

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                    <p class="text-gray-700">{{ __('messages.track_delivery_instruction') }}</p>
                    
                </div>

                <div class="border-t my-4"></div>

                <div class="flex flex-col md:flex-row items-start md:items-center justify-between">
                    <p class="text-gray-700 font-medium">{{ __('messages.order_summary') }}</p>
                    <div class="mt-2 md:mt-0 text-right">
                        <p class="text-xl font-bold">Tk {{ $sell->final_amount }}</p>
                        <p class="text-sm text-gray-500">{{ __('messages.vat_included') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products -->
    <div class="my-8 md:mb-12">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg md:text-xl font-bold">{{ __('messages.top_products') }}</h3>
            <a href="{{ route('front.products.index') }}" class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                <span>{{ __('messages.more_products') }}</span><i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @include('products.section',['sproduct'=>$product])
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
