@extends('layouts.app')
@section('content')
<div class="min-h-screen px-4 sm:px-8 md:px-16 lg:px-36 py-8 sm:py-12">

    <!-- Header -->
    <div class="text-center mb-10">
      <h2 class="text-3xl font-bold text-gray-900">Create Your Seller Account</h2>
      <p class="mt-2 text-gray-600">Fill in the details below to start selling on our platform</p>
    </div>
    @if(session('success'))
        <div id="successAlert" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg flex items-start justify-between">
            <span>{{ session('success') }}</span>
    
            <!-- Close Button -->
            <button onclick="document.getElementById('successAlert').style.display='none'" class="text-green-700 font-bold ml-4">
                ✕
            </button>
        </div>
    @endif
    <!-- Form -->
    <form action="{{ route('front.sellerRegisterPost')}}" method="POST" class="space-y-8" id="review_form">
        @csrf
      
        <!-- First & Last Name -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">First Name <span class="text-red-500">*</span></label>
              <input type="text" name="name"  placeholder="First Name"
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Last Name <span class="text-red-500">*</span></label>
              <input type="text" name="last_name"  placeholder="Last Name"
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
        </div>

        <!-- Email & Mobile -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Email Address <span class="text-red-500">*</span></label>
                <input type="email" name="email"  placeholder="example@domain.com"
                       class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Mobile Number <span class="text-red-500">*</span></label>
                <input type="tel" name="mobile"  placeholder="01xxxxxxxxx"
                       class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
        </div>

        <!-- Shop Name & Trade License -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700">Shop Name <span class="text-red-500">*</span></label>
            <input type="text" name="shop_name"  placeholder="e.g. Rahim Electronics"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700">Trade License <span class="text-red-500">*</span></label>
            <input type="text" name="trade_license"  placeholder="TRAD"
                   class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
          </div>
        </div>

        <!-- Fax & Website -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">FAX</label>
                <input type="text" name="fax" placeholder="FAX"
                       class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Website</label>
                <input type="text" name="website" placeholder="https://www.example.com"
                       class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
        </div>

        <!-- Password & Confirm -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Password <span class="text-red-500">*</span></label>
              <input type="password" name="password" 
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Confirm Password <span class="text-red-500">*</span></label>
              <input type="password" name="password_confirmation" 
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary">
            </div>
        </div>
        
        <!-- Our Mission & Our Vision -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700">Our Mission </label>
              <textarea type="text" name="our_mission" 
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary"></textarea>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Our Vision</label>
              <textarea type="text" name="our_vision" 
                     class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary"></textarea>
            </div>
        </div>

        <!-- Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Business Address</label>
            <textarea name="address" rows="3" placeholder="Enter your full business address"
                      class="mt-1 block w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary resize-none"></textarea>
        </div>

        <!-- Terms -->
        <div class="flex items-start">
            <input id="terms" name="terms" type="checkbox" 
                   class="h-5 w-5 mt-0.5 text-primary focus:ring-primary border-gray-300 rounded">
            <label for="terms" class="ml-3 block text-sm text-gray-700">
              I agree to the <a href="#" class="text-primary hover:underline font-medium">Terms of Service</a> and 
              <a href="#" class="text-primary hover:underline font-medium">Privacy Policy</a>
            </label>
        </div>

        <!-- Submit -->
        <div class="flex justify-center">
            <button type="submit"
                    class="bg-blue-500 text-white py-3 px-8 rounded-md w-full sm:w-auto text-base">
              Register as Seller
            </button>
        </div>

        <div class="text-center text-sm text-gray-600">
            Already have an account? 
            <a href="{{ route('login')}}" class="font-medium text-primary hover:underline">Sign in</a>
        </div>
    </form>
</div>
@endsection
