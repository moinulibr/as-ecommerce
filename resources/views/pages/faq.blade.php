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
                Frequently Asked Questions
            </h1>
            <p class="text-sm sm:text-base text-gray-100 leading-relaxed">
                Find answers to the most common questions
            </p>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="px-4 sm:px-8 md:px-12 lg:px-20 xl:px-32 2xl:px-40 py-12 md:py-20">
        <!-- FAQ Accordion -->
        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            
            @foreach($items as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <button class="w-full px-6 py-5 text-left flex justify-between items-center hover:bg-gray-50 transition-all duration-300 focus:outline-none"
                        onclick="this.parentElement.querySelector('.answer').classList.toggle('hidden')">
                    <span class="text-lg md:text-xl font-semibold text-gray-800">
                        {{ $item->title}}
                    </span>
                    <svg class="w-6 h-6 text-indigo-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div class="answer hidden px-6 pb-6">
                    <p class="text-gray-600 leading-relaxed">
                        {!! $item->description !!}
                    </p>
                </div>
            </div>
            @endforeach

            <!-- Add more FAQs by copying the block above -->
        </div>

        <!-- Still have questions? -->
        <div class="text-center mt-12">
            <p class="text-gray-600 mb-4">Still have questions?</p>
            <a href="mailto:info@softicurity.com" class="inline-flex items-center gap-2 bg-indigo-500 text-white px-8 py-4 rounded-full font-medium hover:bg-indigo-600 transition-all duration-300">
                Get in Touch
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>
        </div>
    </section>
</div>
@endsection

@push('js')

    <!-- Arrow rotation on click -->
    <script>
        document.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.querySelector('svg').classList.toggle('rotate-180');
            });
        });
    </script>

@endpush