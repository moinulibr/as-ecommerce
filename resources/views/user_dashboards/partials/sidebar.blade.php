<div class="lg:block hidden lg:w-64 border-r border-gray-200 p-6">
    <div class="mb-6">
        <p class="text-gray-800 font-medium">{{ __('messages.hey') }},  {{ auth()->user()->name }} </p>
    </div>

    <nav class="space-y-4">
        @include('user_dashboards.partials.nav')
    </nav>
</div>

<!-- Mobile Navigation Dropdown -->
<div class="lg:hidden w-full p-4 border-b border-gray-200">
    <div class="flex items-center justify-between">
        <p class="text-gray-800 font-medium">{{ __('messages.hey') }},  {{ auth()->user()->name }} </p>
        <button id="menuToggle" class="text-gray-700">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <div id="mobileMenu" class="hidden mt-4 space-y-3">
        @include('user_dashboards.partials.nav')
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>