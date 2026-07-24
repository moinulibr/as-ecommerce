@php
    $p_price = getProductDiscount($sproduct);
    $discount_price = $p_price['discount_price'];
    $discount = $p_price['discount'];
@endphp

<div class="relative border rounded overflow-hidden">
    <!-- Product Image -->
    <a href="{{ route('front.products.show', [$sproduct->slug]) }}">
        <img src="{{ $sproduct->image_url }}"
             alt="{{ $sproduct->name }}"
             loading="lazy"
             decoding="async"
             fetchpriority="low"
             class="{{ isset($type) && $type === 'category' ? 'cat-product-image' : 'product-image' }}">
    </a>

    <!-- Discount Badge Top Right -->
    @if($discount_price > 0)
        <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-semibold px-2 py-1 rounded shadow">
            {{ number_format($discount->amount, 0) }}
            {{ $discount->discount_type == 'percentage' ? '%' : 'tk' }} off
        </span>
    @endif
</div>

<!-- Product Info -->
<div class="p-3 md:p-4">
    <a href="{{ route('front.products.show', [$sproduct->slug]) }}">
        <h4 class="font-medium mb-2 text-sm md:text-base line-clamp-2 min-h-[48px]">
            {{ Str::limit($sproduct->name, 25, '..') }}
        </h4>
    </a>

    <!-- Reviews -->
    <div class="flex items-center mb-2">
        <i class="fas fa-star text-yellow-400"></i>
        <span class="ml-1 text-xs md:text-sm">{{ $sproduct->reviews->avg('review') }}</span>
        <span class="ml-1 text-xs text-gray-500">
            ({{ $sproduct->reviews->count() }} {{ __('messages.reviews') }})
        </span>
    </div>

    <!-- Price -->
    <div class="font-bold text-base md:text-lg mb-3 flex items-center space-x-2">
        @if($discount_price > 0)
            <span class="text-red-500">{{ priceFormate($sproduct->price - $discount_price) }}</span>
            <span class="line-through text-gray-400">{{ priceFormate($sproduct->price) }}</span>
        @else
            <span>{{ priceFormate($sproduct->price) }}</span>
        @endif
    </div>

    <!-- Add to Cart -->
    @if($sproduct->type == 'single')
        <form action="{{ route('front.carts.store') }}" method="post" id="cart_form">
            @csrf
            <input type="hidden" name="product_id" value="{{ $sproduct->id }}">
            <input type="hidden" name="variation_id" value="{{ $sproduct->variation->id }}">
            <input type="hidden" name="quantity" value="1">

            <button name="action" value="cart" type="submit"
                    class="w-full bg-gray-900 text-white py-2 rounded hover:bg-gray-800 transition text-sm md:text-base">
                {{ __('messages.add_to_cart') }}
            </button>
        </form>
    @elseif($sproduct->type == 'variable')
        <a href="{{ route('front.products.show', [$sproduct->slug]) }}"
           class="block w-full bg-gray-900 text-white py-2 rounded hover:bg-gray-800 transition text-sm md:text-base text-center">
            {{ __('messages.add_to_cart') }}
        </a>
    @endif
</div>
