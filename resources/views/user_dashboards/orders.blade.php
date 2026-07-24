@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col lg:flex-row border border-gray-200 rounded-lg">
        <!-- Sidebar Navigation - Hidden on mobile, shown as dropdown -->
        @include('user_dashboards.partials.sidebar')
        <!-- Main Content -->
        <div class="flex-1 p-4 lg:p-6">
                <h1 class="text-2xl font-bold mb-6"> {{ $title ?? __('messages.manage_account') }} </h1>

                <!-- Filter Dropdown -->
                @if(isset($order))
                <div class="mb-6 flex items-center">
                    <span class="text-sm text-gray-700 mr-2">{{ __('messages.show') }}</span>
                    <form>
                    <div class="relative">
                        <select name="shipping_status" onchange="this.form.submit()"
                            class="appearance-none bg-gray-100 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">{{ __('messages.all_orders') }}</option>
                            
                            @foreach($status as $k=>$s)
                            <option value="{{ $k}}" {{ $k==request('shipping_status') ? 'selected':''}}>{{ $s }}</option>
                            @endforeach
                        </select>
                        
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                        
                        
                        <select name="payment_status" onchange="this.form.submit()"
                            class="appearance-none bg-gray-100 border border-gray-300 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="">{{ __('messages.payment_status') }}</option>
                            
                            @foreach($pstatus as $pk=>$ps)
                            <option value="{{ $pk}}" {{ $pk==request('payment_status') ? 'selected':''}}>{{ $ps }}</option>
                            @endforeach
                        </select>
                        
                        
                        <div
                            class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                            </svg>
                        </div>
                    </div>
                    </form>
                </div>
                @endif

                <!-- Orders List -->
                <div class="space-y-4" id="orders-list">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left">
                                    <th class="pb-4">{{ __('messages.order_number') }}</th>
                                    <th class="pb-4">{{ __('messages.placed_on') }}</th>
                                    <th class="pb-4">{{ __('messages.shipping_status') }}</th>
                                    <th class="pb-4">{{ __('messages.payment_status') }}</th>
                                    <th class="pb-4">{{ __('messages.payment_method') }}</th>
                                    <th class="pb-4">{{ __('messages.items') }}</th>
                                    <th class="pb-4">{{ __('messages.total') }}</th>
                                    <th class="pb-4">{{ __('messages.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                @include('user_dashboards.partials.order_tr')
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <p>{{ $items->render()}}</p>
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

    const tabs = {
        'all-tab': 'all',
        'to-pay-tab': 'to-pay',
        'to-ship-tab': 'to-ship',
        'to-receive-tab': 'to-receive',
        'to-review-tab': 'to-review'
    };

    Object.keys(tabs).forEach(tabId => {
        document.getElementById(tabId).addEventListener('click', function () {
            // Update active tab styling
            document.querySelectorAll('.flex.min-w-max button').forEach(btn => {
                btn.classList.remove('text-gray-800', 'border-b-2', 'border-gray-800');
                btn.classList.add('text-gray-600');
            });
            this.classList.add('text-gray-800', 'border-b-2', 'border-gray-800');
            this.classList.remove('text-gray-600');

            // Filter orders
            const status = tabs[tabId];
            const orderItems = document.querySelectorAll('.order-item');
            orderItems.forEach(item => {
                if (status === 'all') {
                    item.style.display = 'block';
                } else {
                    item.style.display = item.dataset.status === status ? 'block' : 'none';
                }
            });
        });
    });

</script>


@endpush
