
@foreach($product->reviews as $review)
<div class="border-b border-gray-200 pb-6 mb-6">
    <div class="flex items-center justify-between mb-2">
        <div class="flex items-center">
            <div class="h-8 w-8 bg-gray-200 rounded-full mr-2 flex items-center justify-center text-sm">S
            </div>
            <div>
                <div class="font-medium"> {{ $review->message}} </div>
                <div class="flex text-yellow-400 text-sm">

                    @foreach([1,2,3,4,5] as $i)

                    @if($review->review >=$i)
                    <i class="fas fa-star"></i>
                    @else
                    <i class="far fa-star"></i>
                    @endif
                    
                    @endforeach
                </div>
            </div>
        </div>
        <div class="text-sm text-gray-500">1 day ago</div>
    </div>
    <p class="text-sm mb-3">{{ $review->message}}</p>
    <div class="flex space-x-2">
        <div class="h-16 w-16 bg-gray-200 rounded">
            
                <img src="{{$review->image_url}}">
           
        </div>
    </div>
</div>
@endforeach