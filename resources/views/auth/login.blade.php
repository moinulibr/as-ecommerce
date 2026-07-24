

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amader Sanitary - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome for Google and Facebook icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex flex-col h-screen">

    <!-- Main Content (Logo and Form) -->
    <div class="flex-grow flex flex-col justify-center items-center px-4 space-y-6">
        <!-- Logo Section -->
        <a href="{{ route('front.home') }}">
            <div class="flex justify-center items-center">
                <div class="w-12 h-12 bg-blue-500 mr-3 flex items-center justify-center rounded-full">
                    <span class="text-white text-xl">✦</span>
                </div>
                <h1 class="text-3xl font-semibold">Amader Sanitary</h1>
            </div>
        </a>
        
        <!-- login Form Section -->
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 mb-2" for="email">Email <span
                            class="text-red-500">*</span></label>
                    <input type="email" id="email" value="{{ old('email') }}" required name="email" 
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your email"/>

                    @error('email')
                        <span class="text-red-500 text-sm" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 mb-2" for="password">Password <span
                            class="text-red-500">*</span></label>
                    <input type="password" id="password" name="password" required
                        class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter your password"/>

                    @error('password')
                        <span class="text-red-500 text-sm" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror

                </div>
                <button type="submit" class="w-full bg-gray-900 text-white p-3 rounded-lg hover:bg-gray-800 mb-4">Login</button>

                <!-- Social Sign-In Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-6">
                    <a href="{{ route('socialite.index')}}"
                        class="w-full text-black p-3 rounded-lg border border-gray-300 hover:bg-blue-500 flex items-center justify-center gap-2 inline-flex">
                        <i class="fab fa-google text-[#db4437]"></i>
                        <span>Sign in with Google</span>
                    </a>
                </div>                
                
                <p class="text-center mt-4">
                    Don't have and account? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Sign up</a>
                    Don't have and account? <a href="{{ route('password.request') }}" class="text-blue-500 hover:underline">Password Forget ?</a>
                </p>
            </form>
        </div>
    </div>

    <!-- Footer (Fixed at the bottom) -->
    <footer class="p-4 text-center text-gray-600 text-sm">
        <a href="{{ route('front.termCondition')}}" class="hover:underline">CONDITIONS OF USE</a>
        <a href="{{ route('front.privacyPolicy')}}" class="ml-4 hover:underline">PRIVACY</a>
        <a href="{{ route('front.contactUs')}}" class="ml-4 hover:underline">HELP</a>
        <p class="mt-2">©{{ date('Y') }}, Amader Sanitary Inc.</p>
    </footer>

</body>

</html>
