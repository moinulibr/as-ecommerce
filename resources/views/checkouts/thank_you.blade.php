@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">

    {{--
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
    --}}

    <div class="max-w-2xl w-full mx-auto my-8 p-4 md:p-6 bg-white rounded-2xl shadow-sm border border-slate-100">
        <!-- Success Icon Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center bg-emerald-100 text-emerald-600 rounded-full w-20 h-20 mb-4 ring-8 ring-emerald-50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">
                {{ __('messages.thank_you_purchase') }}
            </h1>
            <p class="text-slate-500 mt-2 text-sm md:text-base">
                {{ __('messages.order_number_is') }} <span class="font-bold text-slate-800">#{{ $sell->invoice_no }}</span>
            </p>
        </div>

        <!-- Main Order Details Card -->
        <div class="bg-slate-50 rounded-xl p-5 border border-slate-200/60 mb-6">
            <!-- Date & Status Row -->
            <div class="flex flex-wrap items-center justify-between gap-2 pb-4 border-b border-slate-200/80">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Order Date</span>
                    <span class="text-sm font-semibold text-slate-700">{{ dateFormate($sell->transaction_date) }}</span>
                </div>
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        Order Placed
                    </span>
                </div>
            </div>

            <!-- Customer & Shipping Details (Directly via $sell->contact) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 py-4 border-b border-slate-200/80 text-sm">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Customer Info</span>
                    <p class="font-bold text-slate-800">{{ $sell->contact->name ?? 'N/A' }}</p>
                    <p class="text-slate-600 font-mono text-xs mt-0.5">{{ $sell->contact->mobile ?? '' }}</p>
                    @if(!empty($sell->contact->email))
                        <p class="text-slate-500 text-xs mt-0.5">{{ $sell->contact->email }}</p>
                    @endif
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Shipping Address</span>
                    <p class="text-slate-700 leading-relaxed font-medium">{{ $sell->contact->address ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Customer Order Note -->
            @if(!empty($sell->note))
                <div class="py-3 border-b border-slate-200/80">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Order Note</span>
                    <p class="text-xs text-slate-600 italic bg-white p-2.5 rounded-lg border border-slate-200/60">"{{ $sell->note }}"</p>
                </div>
            @endif

            <!-- Payment & Total Section -->
            <div class="pt-4 flex items-end justify-between">
                <div>
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Payment Method</span>
                    <span class="text-sm font-semibold text-slate-700 capitalize">
                        {{ str_replace('_', ' ', $sell->payment_method) }}
                    </span>
                </div>
                <div class="text-right">
                    <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Amount</span>
                    <span class="text-2xl font-bold text-blue-600 tracking-tight">{{ priceFormate($sell->final_amount) }}</span>
                    <span class="text-[11px] text-slate-400 block">{{ __('messages.vat_included') }}</span>
                </div>
            </div>
        </div>

        <!-- Instruction Info Note -->
        <div class="flex items-start gap-3 p-4 bg-blue-50/70 rounded-xl border border-blue-100 mb-8 text-xs md:text-sm text-blue-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>
                {{ __('messages.track_delivery_instruction') }} <br/>
                ("আপনার অর্ডারটি সফলভাবে গ্রহণ করা হয়েছে। খুব শীঘ্রই আমাদের একজন প্রতিনিধি আপনার সাথে যোগাযোগ করবেন।")
            </span>
        </div>

        <!-- Continue Shopping Button -->
        <div class="flex justify-center">
            <a href="{{ route('front.products.index') }}" 
            class="inline-flex items-center justify-center gap-2 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-base rounded-xl shadow-md hover:shadow-lg transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span>{{ __('messages.continue_shopping') }}</span>
            </a>
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
