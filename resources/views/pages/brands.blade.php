@extends('layouts.app')
@section('content')
<div class="min-h-screen">
   <div class="min-h-screen py-10">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Page Title -->
        <div class="text-center mb-10">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-gray-900">
                All Brands
            </h1>
            <p class="text-gray-600 mt-2 text-sm sm:text-base">
                Browse all our available brands in one place.
            </p>
        </div>

        <!-- Brands Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-5 sm:gap-6">

            @forelse($brands as $brand)
                <a href="{{ route('front.products.index',['brand_id' => $brand->id]) }}"
                    class="relative border border-gray-200 rounded-xl p-4 hover:shadow-lg transition-shadow duration-200 bg-white group">
            
                    <!-- 10% Off Badge -->
                    @if($brand->discount)
                    <span class="absolute top-0 right-0 bg-red-600 text-white text-[12px] font-semibold px-2 py-1 rounded">
                        {{ number_format($brand->discount->amount, 0) }} {{ $brand->discount->discount_type == 'percentage' ? '%' : 'tk' }} off
                    </span>
                    @endif
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg overflow-hidden border border-gray-200 mb-3">
                            <img src="{{ $brand->image_url }}" alt="{{ $brand->name }}" 
                                 class="w-full h-full object-contain group-hover:scale-105 transition-transform duration-200">
                        </div>
            
                        <p class="font-semibold text-sm sm:text-base text-gray-800 text-center">
                            {{ $brand->name }}
                        </p>
                    </div>
            
                </a>
            @empty
                <p class="text-center col-span-full text-gray-500">No brands available.</p>
            @endforelse


        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $brands->links() }}
        </div>

    </div>
</div>

</div>
@endsection