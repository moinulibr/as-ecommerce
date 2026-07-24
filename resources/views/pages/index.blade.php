@extends('layouts.app')
@section('content')
<!-- Hero Section -->
<section 
    class="relative md:py-24 py-12 text-center text-white bg-cover bg-center bg-no-repeat"
    style="background-image: url('{{ asset('img/hero-section.jpg') }}');">
    
    <!-- Transparent Overlay -->
    <div class="absolute inset-0 bg-black/40"></div>

    <div class="relative container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl">
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4">
            {{ $page ? $page->title : '' }}
        </h1>
        <p class="text-sm sm:text-base text-gray-100 leading-relaxed">
            We are dedicated to providing high-quality, hygienic sanitary products that prioritize safety,
            comfort, and sustainability for our customers.
        </p>
    </div>
</section>


<!-- Company Story -->
<section class="bg-white py-6 md:py-12 px-12 md:px-12 rounded-lg">
    {!! $page ? $page->description : '' !!}
</section>

@endsection