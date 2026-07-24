@extends('layouts.app')
@section('content')
<div class="min-h-screen">
    <!-- Hero Section -->
    <section 
        class="relative md:py-24 py-12 text-center text-white bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('img/hero-section.jpg') }}');">
        
        <!-- Transparent Overlay -->
        <div class="absolute inset-0 bg-black/40"></div>
    
        <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4">
                Contact Us
            </h1>
            <p class="text-sm sm:text-base text-gray-100 leading-relaxed">
                We'd love to hear from you! Reach out with
                any questions or feedback about our sanitary products.
            </p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-8 sm:py-12">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
            <!-- Contact Form -->
            <div class="bg-white shadow-md rounded-lg p-6 mb-8">
                <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-4">Send Us a Message</h2>
                @if(session('success'))
                    <div id="successAlert" class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg flex items-start justify-between">
                        <span>{{ session('success') }}</span>
                
                        <!-- Close Button -->
                        <button onclick="document.getElementById('successAlert').style.display='none'" class="text-green-700 font-bold ml-4">
                            ✕
                        </button>
                    </div>
                @endif

                <div>
                    <form action="{{ route('front.contact')}}" method="POST" id="review_form">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                <input type="text" id="name" name="name" placeholder="Your Name"
                                    class="mt-1 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input type="email" id="email" name="email" placeholder="Your Email"
                                    class="mt-1 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="subject" class="block text-sm font-medium text-gray-700">Subject</label>
                            <input type="text" id="subject" name="subject" placeholder="Subject"
                                class="mt-1 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="Your Message"
                                class="mt-1 w-full py-2 px-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-blue-600 text-white font-medium py-2 px-5 rounded-md hover:bg-blue-700 transition">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-4">Contact Information</h2>
                    <ul class="space-y-3 text-sm sm:text-base text-gray-700">
                        <li class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-500"></i>
                            Email: {{ $setting->email }}
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-phone mr-2 text-blue-500"></i>
                            Phone: {{ $setting->phone }}
                        </li>
                        <li class="flex items-center">
                            <i class="fab fa-whatsapp mr-2 text-blue-500"></i>
                            WhatsApp:
                            <a href="https://wa.me/{{ $setting->whats_app_no }}" 
                               target="_blank"
                               class="text-blue-500 hover:underline">
                                Message us
                            </a>
                        </li>

                        <li class="flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-500"></i>
                            Address: {{ $setting->address }}
                        </li>
                    </ul>
                </div>
                <div>
                    <h2 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-900 mb-4">Our Location</h2>
                    <div class="w-full h-64 bg-gray-200 rounded-lg overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.899355576091!2d90.3882926149811!3d23.75117198459291!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087c7f1e7%3A0x4e9269464d1d2b99!2sDhaka%2C%20Bangladesh!5e0!3m2!1sen!2sbd!4v1697648254152!5m2!1sen!2sbd"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection