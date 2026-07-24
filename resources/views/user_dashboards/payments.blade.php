@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col lg:flex-row border border-gray-200 rounded-lg">
        <!-- Sidebar Navigation -->
        @include('user_dashboards.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 p-4 lg:p-6">
            <h1 class="text-2xl font-bold mb-6"> {{ __('messages.my_payment_options') }} </h1>
        
            <!-- Wallet List -->
            <div id="wallet">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Wallet Card 1 -->
                    <div class="w-full">
                        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between">
                                <div class="text-2xl font-bold mr-4">bKash</div>
                                <button class="text-red-500 text-sm font-medium hover:text-red-600 focus:outline-none">
                                    {{ __('messages.delete') }}
                                </button>
                            </div>
                            <div>
                                <img src="https://logos-world.net/wp-content/uploads/2024/10/Bkash-Logo.jpg" alt="" class="w-48 h-28">
                                <div class="text-gray-700 text-lg">01323****332</div>
                            </div>
                        </div>
                    </div>
                    <!-- Wallet Card 2 -->
                    <div class="w-full">
                        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between">
                                <div class="text-2xl font-bold mr-4">Rocket</div>
                                <button class="text-red-500 text-sm font-medium hover:text-red-600 focus:outline-none">
                                    {{ __('messages.delete') }}
                                </button>
                            </div>
                            <div>
                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXzzzZtGqFaqJSqPkiUZsnKfzzzLTWvPfC8w&s" alt="" class="w-48 h-28">
                                <div class="text-gray-700 text-lg">01323****332</div>
                            </div>
                        </div>
                    </div>
                    <!-- Wallet Card 3 -->
                    <div class="w-full">
                        <div class="bg-white border border-gray-300 rounded-lg p-4 shadow-sm">
                            <div class="flex justify-between">
                                <div class="text-2xl font-bold mr-4">Nagad</div>
                                <button class="text-red-500 text-sm font-medium hover:text-red-600 focus:outline-none">
                                    {{ __('messages.delete') }}
                                </button>
                            </div>
                            <div>
                                <img src="https://download.logo.wine/logo/Nagad/Nagad-Logo.wine.png" alt="" class="w-48 h-28">
                                <div class="text-gray-700 text-lg">01323****332</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>              
        
            <!-- Add Payment Button -->
            <div class="flex flex-col sm:flex-row gap-4">
                <button id="addPaymentOptions"
                    class="bg-gray-900 text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition-colors w-full sm:w-auto">
                    {{ __('messages.add_payment_option') }}
                </button>
            </div>
        
            <!-- Add Payment Option Modal -->
            <div id="addPaymentOptionModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-8 w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.add_payment_option') }}</h2>
                        <button class="closeModal text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form class="space-y-4">
                        <div>
                            <label for="paymentType" class="block text-sm font-medium text-gray-700">{{ __('messages.payment_type') }}</label>
                            <select id="paymentType" name="paymentType"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-pink-500 focus:border-pink-500">
                                <option value="">{{ __('messages.select_type') }}</option>
                                <option value="bkash">bKash</option>
                                <option value="nagad">Nagad</option>
                                <option value="rocket">Rocket</option>
                            </select>
                        </div>
        
                        <div>
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700">{{ __('messages.phone_number') }}</label>
                            <input type="number" id="phoneNumber" name="phoneNumber" placeholder="01xxxxxxxxx"
                                class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-pink-500 focus:border-pink-500">
                        </div>
        
                        <div class="flex justify-end pt-4">
                            <button type="button"
                                class="closeModal mr-2 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">{{ __('messages.cancel') }}</button>
                            <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white rounded-md">{{ __('messages.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Mobile menu toggle
    document.getElementById('menuToggle').addEventListener('click', function () {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });

    const addPaymentOptionModal = document.getElementById('addPaymentOptionModal');
    const addPaymentOptions = document.getElementById('addPaymentOptions');
    const closeButtons = document.querySelectorAll('.closeModal');

    addPaymentOptions.addEventListener('click', () => {
        addPaymentOptionModal.classList.remove('hidden');
    });

    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            addPaymentOptionModal.classList.add('hidden');
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === addPaymentOptionModal) {
            addPaymentOptionModal.classList.add('hidden');
        }
    });
</script>
@endpush
