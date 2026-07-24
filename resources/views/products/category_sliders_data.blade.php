@foreach($pcats as $cat)
    @if($cat->productwithprice->isNotEmpty())
        <div class="category-slider-wrapper mb-8 md:mb-12">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg md:text-xl font-bold category-name">{{ $cat->name }}</h3>
                
                <div class="flex gap-2 items-center arrow-controls">
                    <a href="{{ route('front.products.index', ['cat_id' => $cat->id]) }}"
                       class="text-gray-600 hover:text-blue-500 transition flex items-center text-sm md:text-base">
                        <span>{{ __('messages.see_more') }}</span>
                    </a>
                    <button class="category-products-slick-prev custom-arrow bg-white border border-gray-200 rounded-full w-7 h-7 md:w-8 md:h-8 flex items-center justify-center">←</button>
                    <button class="category-products-slick-next custom-arrow bg-white border border-gray-200 rounded-full w-7 h-7 md:w-8 md:h-8 flex items-center justify-center">→</button>
                </div>
            </div>

            <div class="category-product-slider">
                @foreach($cat->productwithprice as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden mx-2 border">
                        @include('products.section', ['sproduct' => $product, 'type' => 'category'])
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endforeach