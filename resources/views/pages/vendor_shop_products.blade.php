<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    @forelse($products as $product)
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            @include('products.section',['sproduct'=>$product])
        </div>
    @empty
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            <p class="text-white text-lg">No Products Found</p>
        </div>
    @endforelse
</div>


<!-- Pagination -->
@if($products->hasPages())
    <div class="mt-6 flex justify-center space-x-2">
        @if($products->onFirstPage())
            <span class="px-3 py-1 border border-gray-300 rounded text-gray-400 cursor-not-allowed"> Previous </span>
        @else
            <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition"> Previous </a>
        @endif @if($products->hasMorePages())
            <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1 border border-gray-300 rounded hover:bg-gray-100 transition"> Next </a>
        @else
            <span class="px-3 py-1 border border-gray-300 rounded text-gray-400 cursor-not-allowed"> Next </span>
        @endif
    </div>
@endif
