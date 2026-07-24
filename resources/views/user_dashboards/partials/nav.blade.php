<a href="{{ route('front.dashboards.index')}}" 
   class="block {{ Route::is('front.dashboards.index') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.manage_account') }}
</a>

<a href="{{ route('front.dashboards.create')}}" 
   class="block {{ Route::is('front.dashboards.create') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.my_profile') }}
</a>

<a href="{{ route('front.user-address.index')}}" 
   class="block {{ Route::is('front.user-address.index') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.shipping_address') }}
</a>

<!--<a href="{{ route('front.user-payments.index')}}" -->
<!--   class="block {{ Route::is('front.user-payments.index') ? 'text-blue-500' : 'text-gray-700' }}">-->
<!--   {{ __('messages.payment_options') }}-->
<!--</a>-->

<a href="{{ route('front.user-orders.index')}}" 
   class="block {{ Route::is('front.user-orders.index') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.my_orders') }}
</a>

<a href="{{ route('front.returnOrder')}}" 
   class="block {{ Route::is('front.returnOrder') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.my_returns') }}
</a>

<a href="{{ route('front.cancelledOrder')}}" 
   class="block {{ Route::is('front.cancelledOrder') ? 'text-blue-500' : 'text-gray-700' }}">
   {{ __('messages.my_cancellations') }}
</a>

<a onclick="event.preventDefault();document.getElementById('logout-form').submit();"  
   href="#" class="block text-red-700">
   {{ __('messages.logout') }}
</a>
