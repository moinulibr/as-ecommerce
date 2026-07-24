<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('messages.order_details') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-4 max-w-4xl">
        <!-- Header -->
        <header class="bg-white shadow rounded-lg p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">{{ __('messages.order_details') }}</h1>
            <p class="text-gray-600">
                {{ __('messages.order_number') }} #{{ $item->invoice_no }} | 
                {{ __('messages.placed_on') }} {{ dateFormate($item->transaction_date) }}
            </p>
        </header>

        <!-- Order Summary -->
        <section class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('messages.order_summary') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-gray-600"><span class="font-medium">{{ __('messages.shipping_status') }}:</span> {{ $item->shipping_status }}</p>
                    <p class="text-gray-600"><span class="font-medium">{{ __('messages.total') }}:</span> {{ priceFormate($item->final_amount) }} </p>
                    <p class="text-gray-600"><span class="font-medium">{{ __('messages.payment_method') }}:</span> {{ str_replace("_", " ", $item->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-gray-600"><span class="font-medium">{{ __('messages.estimated_delivery') }}:</span> {{ dateFormate($item->transaction_date) ?? '' }}</p>
                    <p class="text-gray-600"><span class="font-medium">{{ __('messages.delivery_method') }}:</span> {{ $item->delivery->title ?? '' }}</p>
                </div>
            </div>
        </section>

        <!-- Customer Details -->
        @if($item->shipping)
        <section class="bg-white shadow rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('messages.customer_details') }}</h2>
            <p class="text-gray-600"><span class="font-medium">{{ __('messages.name') }}:</span> {{ $item->shipping->name }}</p>
            <p class="text-gray-600"><span class="font-medium">{{ __('messages.email') }}:</span> {{ $item->user->email }}</p>
            <p class="text-gray-600"><span class="font-medium">{{ __('messages.phone_number') }}:</span> {{ $item->shipping->phone }}</p>
            <p class="text-gray-600"><span class="font-medium">{{ __('messages.shipping_address') }}:</span> {{ $item->shipping->address }}</p>
        </section>
        @endif

        <!-- Order Items -->
        <section class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">{{ __('messages.order_items') }}</h2>
            <div class="space-y-4">
                @foreach($item->lines as $line)
                <div class="flex justify-between items-center border-b pb-4">
                    <div class="flex items-center">
                        <img src="{{ $line->product->image_url }}" alt="Product Image" class="w-20 h-20 object-cover rounded mr-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-800">
                                {{ $line->product->name }} 
                                {{ $line->product->type == 'variable' ? $line->variation->name : '' }}
                            </h3>
                            <p class="text-gray-600">{{ __('messages.quantity') }}: {{ $line->quantity }}</p>
                            <p class="text-gray-600">{{ __('messages.price') }}: {{ $line->price }}</p>
                        </div>
                    </div>
                    <p class="text-gray-800 font-medium">{{ priceFormate($line->price * $line->quantity) }}</p>
                </div>
                @endforeach
            </div>

            <!-- Total -->
            <div class="flex justify-end mt-4">
                <div class="text-right">
                    <p class="text-gray-600">{{ __('messages.items_total') }}: {{ priceFormate($item->final_amount + $item->discount_amount - $item->shipping_charge) }} </p>
                    <p class="text-gray-600">{{ __('messages.delivery_fee') }}: {{ priceFormate($item->shipping_charge) }}</p>
                    @if($item->discount_amount)
                    <p class="text-gray-600">{{ __('messages.discount') }}: {{ priceFormate($item->discount_amount) }}</p>
                    @endif
                    <p class="text-lg font-bold text-gray-800">{{ __('messages.total') }}: {{ priceFormate($item->final_amount) }}</p>
                </div>
            </div>
        </section>

        <!-- Actions -->
        <div class="mt-6 flex justify-end space-x-4">
            <a href="{{ route('front.dashboards.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">{{ __('messages.dashboard') }}</a>
            <a href="{{ route('front.contactUs') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">{{ __('messages.contact_us') }}</a>
        </div>
    </div>
</body>
</html>
