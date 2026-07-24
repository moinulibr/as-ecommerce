<div class="bg-white rounded-lg p-6 w-full max-w-md">
    <div class="flex justify-between items-center mb-4">
        <h2 id="modalTitle" class="text-xl font-bold text-gray-800">Add Address</h2>
        <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <form id="cart_form" action="{{ route('front.user-address.store')}}" method="post">
        @csrf
        <div class="mb-4">
            <label for="fullName" class="block text-gray-700 font-medium mb-1">Full Name</label>
            <input type="text" id="fullName" name="name" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="mb-4">
            <label for="address" class="block text-gray-700 font-medium mb-1">Address</label>
            <textarea id="address" name="address" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required></textarea>
        </div>
        <div class="mb-4">
            <label for="phone" class="block text-gray-700 font-medium mb-1">Phone Number</label>
            <input type="tel" id="phone" name="phone" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500" required>
        </div>
        <div class="flex justify-end space-x-2">
            <button type="button" onclick="closeModal()" class="bg-gray-300 text-gray-800 px-4 py-2 rounded hover:bg-gray-400">Cancel</button>
            <button type="submit" form="cart_form" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Save</button>
        </div>
    </form>
</div>