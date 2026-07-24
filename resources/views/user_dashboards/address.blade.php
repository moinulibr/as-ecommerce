@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col lg:flex-row border border-gray-200 rounded-lg">
        <!-- Sidebar Navigation -->
        @include('user_dashboards.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 p-4 lg:p-6">
            <h1 class="text-2xl font-bold mb-6">{{ __('messages.shipping_address') }}</h1>

            <!-- Address Table -->
            <div class="overflow-x-auto">
                <table class="w-full responsive-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="py-4 px-6 text-left text-gray-500 font-medium">{{ __('messages.full_name') }}</th>
                            <th class="py-4 px-6 text-left text-gray-500 font-medium">{{ __('messages.address') }}</th>
                            <th class="py-4 px-6 text-left text-gray-500 font-medium">{{ __('messages.phone_number') }}</th>
                            <th class="py-4 px-6 text-left text-gray-500 font-medium">{{ __('messages.action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($items as $item)
                        <tr>
                            <td class="py-4 px-6" data-label="{{ __('messages.full_name') }}">{{ $item->name }}</td>
                            <td class="py-4 px-6" data-label="{{ __('messages.address') }}">{{ $item->address }}</td>
                            <td class="py-4 px-6" data-label="{{ __('messages.phone_number') }}">{{ $item->phone }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('front.user-address.edit',[$item->id])}}" 
                                   class="text-blue-500 font-medium hover:underline btn_modal">
                                   {{ __('messages.edit') }}
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Add New Address Button -->
            <div class="flex justify-center py-8">
                <a href="{{ route('front.user-address.create')}}" 
                   class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition btn_modal">
                   {{ __('messages.add_new_address') }}
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal (optional) -->
<div id="addressModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
</div>
@endsection

@push('js')
<script>
    // Mobile menu toggle
    document.getElementById('menuToggle').addEventListener('click', function () {
        const mobileMenu = document.getElementById('mobileMenu');
        mobileMenu.classList.toggle('hidden');
    });

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
