@extends('layouts.app')
@section('content')

<div class="container mx-auto px-3 sm:px-4 py-4">
    <div class="flex flex-col lg:flex-row border border-gray-200 rounded-xl bg-gray-50 overflow-hidden">

        <!-- Sidebar (UNCHANGED) -->
        @include('user_dashboards.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 p-4 sm:p-6">

            <h1 class="text-xl sm:text-2xl font-bold mb-6">
                {{ __('messages.manage_account') }}
            </h1>

            <!-- Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- User Information Card -->
                <div class="bg-white rounded-xl border shadow-sm p-5">
                    
                    <!-- Profile Image -->
                    <div class="flex flex-col items-center mb-4">
                        <img
                            src="{{ $user->image ? asset('images/profile/'.$user->image) : asset('img/avatar.jpg') }}"
                            class="w-24 h-24 rounded-full object-cover border-4 border-gray-200 shadow"
                            alt="User Image"
                        >
                        <h2 class="mt-3 text-lg font-semibold">
                            {{ $user->name }}
                        </h2>
                        <p class="text-sm text-gray-500">
                            {{ $user->email }}
                        </p>
                    </div>

                    <!-- User Info -->
                    <div class="space-y-2 text-sm text-gray-700">
                        <p class="flex justify-between">
                            <span class="font-medium">Phone</span>
                            <span>{{ $user->mobile ?? 'N/A' }}</span>
                        </p>

                        <p class="flex justify-between">
                            <span class="font-medium">Birthday</span>
                            <span>
                                {{ $user->dob ? \Carbon\Carbon::parse($user->dob)->format('d M Y') : 'N/A' }}
                            </span>
                        </p>

                        <p class="flex justify-between">
                            <span class="font-medium">Gender</span>
                            <span>{{ ucfirst($user->gender) ?? 'N/A' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Recent Orders Card -->
                <div class="lg:col-span-2 bg-white rounded-xl border shadow-sm p-5">
                    <h2 class="text-lg font-semibold mb-4">
                        {{ __('messages.recent_orders') }}
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-gray-700">
                                <tr>
                                    <th class="px-3 py-2 text-left">{{ __('messages.order_number') }}</th>
                                    <th class="px-3 py-2">{{ __('messages.placed_on') }}</th>
                                    <th class="px-3 py-2">{{ __('messages.shipping_status') }}</th>
                                    <th class="px-3 py-2 text-center">{{ __('messages.items') }}</th>
                                    <th class="px-3 py-2 text-right">{{ __('messages.total') }}</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y">
                                @forelse($items->take(5) as $item)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-3 py-3 font-medium">
                                            {{ $item->invoice_no }}
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            {{ dateFormate($item->transaction_date) }}
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs
                                                @if($item->shipping_status == 'delivered')
                                                    bg-green-100 text-green-700
                                                @else
                                                    bg-yellow-100 text-yellow-700
                                                @endif">
                                                {{ ucfirst($item->shipping_status) }}
                                            </span>
                                        </td>

                                        <td class="px-3 py-3 text-center">
                                            {{ $item->lines->count() }}
                                        </td>

                                        <td class="px-3 py-3 text-right font-semibold">
                                            {{ priceFormate($item->final_amount) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-6 text-gray-500">
                                            No recent orders found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
