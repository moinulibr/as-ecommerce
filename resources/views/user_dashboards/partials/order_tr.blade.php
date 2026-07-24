<tr class="border-t text-sm md:text-base">
    <td class="py-3 px-2 whitespace-nowrap">{{ $item->invoice_no }}</td>

    <td class="py-3 px-2 whitespace-nowrap">
        {{ dateFormate($item->transaction_date) }}
    </td>

    <td class="py-3 px-2">{{ ucfirst($item->shipping_status) }}</td>
    <td class="py-3 px-2">{{ ucfirst($item->payment_status) }}</td>
    <td class="py-3 px-2">{{ ucwords(str_replace('_', ' ', $item->payment_method)) }}</td>

    <td class="py-3 px-2 text-center">
        {{ $item->lines->count() }}
    </td>

    <td class="py-3 px-2 whitespace-nowrap">
        {{ priceFormate($item->final_amount) }}
    </td>

    <td class="py-3 px-2 text-right space-x-2 flex flex-col items-end md:flex-row md:items-center md:space-x-3 md:space-y-0 space-y-2">

        <a href="{{ route('front.user-orders.show', [$item->id]) }}"
            class="border border-blue-500 text-blue-500 px-3 py-1 rounded hover:bg-blue-50 transition">
            {{ __('messages.details') }}
        </a>

        @if ($item->cancel_request == 1) 
            <button 
                class="border border-gray-500 text-gray-500 px-3 py-1 rounded bg-gray-100 cursor-pointer viewCancelBtn"
                data-id="{{ $item->id }}"
                data-note="{{ $item->cancel_note }}">
                Cancelled
            </button>
        @else
            <!-- Normal Cancel -->
            <button 
                class="border border-red-500 text-red-500 px-3 py-1 rounded hover:bg-red-50 transition cancelRequestBtn" 
                data-id="{{ $item->id }}">
                {{ __('messages.cancel') }}
            </button>
        @endif
    </td>
</tr>
<div id="toastSuccess" 
     class="fixed top-5 right-5 bg-green-600 text-white px-4 py-2 rounded shadow-lg hidden transition-all duration-300">
</div>

<!-- Cancel Request Popup Modal -->
<div id="cancelRequestModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">

    <div class="bg-white w-96 p-6 rounded shadow-lg">

        <h2 class="text-xl font-bold mb-3">Cancel Order Request</h2>

        <label class="text-sm text-gray-600">Write Note:</label>
        <textarea id="cancel_note" 
                  class="w-full border rounded p-2 mb-2" 
                  placeholder="Why you want to cancel?" required></textarea>
        <p id="noteError" class="text-red-500 text-sm hidden mb-4"></p>

        <label class="flex items-center space-x-2 mb-4">
            <input type="checkbox" id="agreeCheck">
            <span class="text-sm text-gray-700">I confirm this cancellation request</span>
        </label>

        <input type="hidden" id="sell_id">

        <div class="flex justify-end space-x-3">
            <button id="closeCancelPopup" class="px-4 py-2 bg-gray-300 rounded">
                Close
            </button>

            <button id="submitCancelRequest" 
                    class="px-4 py-2 bg-red-500 text-white rounded">
                Submit
            </button>
        </div>
    </div>
</div>

<!-- View Cancel Info Modal -->
<div id="viewCancelInfoModal" 
     class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">

    <div class="bg-white w-96 p-6 rounded shadow-lg">

        <h2 class="text-xl font-bold mb-3">Cancellation Details</h2>

        <label class="text-sm text-gray-600">Cancel Note:</label>
        <textarea id="show_cancel_note" 
                  class="w-full border rounded p-2 mb-3 bg-gray-100"
                  readonly></textarea>

        <label class="flex items-center space-x-2 mb-5">
            <input type="checkbox" checked disabled>
            <span class="text-sm text-gray-700">Cancellation Requested</span>
        </label>

        <div class="flex justify-end">
            <button id="closeViewCancelInfo" 
                class="px-4 py-2 bg-gray-300 rounded">
                Close
            </button>
        </div>
    </div>
</div>

@pushOnce('js')
<script>

    // Existing code remains same...

    document.addEventListener('click', function(e) {

        // -----------------------------------
        // OPEN VIEW CANCEL INFO MODAL
        // -----------------------------------
        if (e.target.classList.contains('viewCancelBtn')) {

            let note = e.target.dataset.note;

            document.getElementById('show_cancel_note').value = note;
            document.getElementById('viewCancelInfoModal').classList.remove('hidden');
        }

        // CLOSE VIEW INFO MODAL
        if (e.target.id === 'closeViewCancelInfo') {
            document.getElementById('viewCancelInfoModal').classList.add('hidden');
        }


        // -----------------------------------
        // OPEN NORMAL CANCEL REQUEST POPUP
        // -----------------------------------
        if (e.target.classList.contains('cancelRequestBtn')) {
            document.getElementById('sell_id').value = e.target.dataset.id;
            document.getElementById('cancelRequestModal').classList.remove('hidden');
        }

        // CLOSE CANCEL REQUEST POPUP
        if (e.target.id === 'closeCancelPopup') {
            document.getElementById('cancelRequestModal').classList.add('hidden');
            document.getElementById('noteError').classList.add('hidden');
            document.getElementById('noteError').innerText = "";
        }


        // -----------------------------------
        // SUBMIT NORMAL CANCEL REQUEST
        // -----------------------------------
        if (e.target.id === 'submitCancelRequest') {

            let id = document.getElementById('sell_id').value;
            let note = document.getElementById('cancel_note').value.trim();
            let agree = document.getElementById('agreeCheck').checked;

            let errorBox = document.getElementById('noteError');

            errorBox.classList.add('hidden');
            errorBox.innerText = "";

            if (!note) {
                errorBox.innerText = "Please write your cancellation reason.";
                errorBox.classList.remove('hidden');
                return;
            }

            if (!agree) {
                errorBox.innerText = "Please check the agreement box.";
                errorBox.classList.remove('hidden');
                return;
            }

            fetch("{{ route('front.order.cancel.request') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    sell_id: id,
                    note: note,
                    agree: agree
                })
            })
            .then(res => res.json())
            .then(data => {
               showToast(data.message);
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            })
            .catch(err => console.error(err));
        }

    });
    
    function showToast(message) {
        let toast = document.getElementById('toastSuccess');
        toast.innerText = message;
        toast.classList.remove('hidden');
    
        // Fade out after 2.5 seconds
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 2500);
    }


</script>
@endPushOnce
