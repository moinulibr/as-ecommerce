@foreach($items as $product)
<div class="bg-white rounded-lg shadow overflow-hidden">
                    
    @include('products.section',['sproduct'=>$product])
    
</div>
@endforeach