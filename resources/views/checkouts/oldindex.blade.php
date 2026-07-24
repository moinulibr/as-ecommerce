@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <form method="post" action="{{ route('front.checkouts.store')}}" id="checkout_form">
            @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 py-6" id="checkout_data">
        
        
    </div>
    </form>
</div>

<!-- Modal -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">

    
</div>
@endsection

@push('js')
<script>
$(document).on('change', 'input.shipping_id, input.delivery_id', function() {


    let shipping_id = $("input.shipping_id:checked").val();
    let delivery_id = $("input.delivery_id:checked").val();


    $.ajax({
        url: "{{ route('front.storeSession') }}",   // route for storing session
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            delivery_id: delivery_id,
            shipping_id: shipping_id,
        },
        success: function (response) {
            console.log("Saved in session:", response);
        }
    });
});
</script>

<script>
    
    function openModal() {
        const modal = document.getElementById('addressModal');
        modal.classList.remove('hidden');
    }
    
    function closeModal() {
        const modal = document.getElementById('addressModal');
        modal.classList.add('hidden');
    }
    
</script>



@endpush