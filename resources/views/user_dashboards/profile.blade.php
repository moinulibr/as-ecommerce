@extends('layouts.app')
@section('content')
<div class="container mx-auto p-4">
    <div class="flex flex-col lg:flex-row border border-gray-200 rounded-lg">
        <!-- Sidebar Navigation -->
        @include('user_dashboards.partials.sidebar')

        <!-- Main Content -->
        <div class="flex-1 p-4 lg:p-6">
            <h1 class="text-2xl font-bold mb-6">{{ __('messages.my_profile') }}</h1>
            @if(session('success'))
                <div id="successAlert"
                     class="relative mb-4 p-4 rounded-md bg-green-100 text-green-700 flex items-center justify-between">
                    
                    <span>{{ session('success') }}</span>
            
                    <!-- Close Button -->
                    <button onclick="closeSuccessAlert()" class="ml-4 text-green-700 hover:text-green-900">
                        &times;
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div class="relative mb-4 p-4 rounded-md bg-red-100 text-red-700 flex items-center justify-between">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-red-700 hover:text-red-900">&times;</button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="relative mb-4 p-4 rounded-md bg-red-100 text-red-700 flex items-center justify-between">
                    <ul class="list-disc ml-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button onclick="this.parentElement.remove()" class="ml-4 text-red-700 hover:text-red-900">&times;</button>
                </div>
            @endif


            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                <!-- First column -->
                <div>
                    <p class="text-gray-500 mb-2">{{ __('messages.full_name') }}</p>
                    <p class="text-gray-900 text-lg">{{ $user->name }}</p>
                </div>

                <!-- Second column -->
                <div>
                    <p class="text-gray-500 mb-2">{{ __('messages.email_address') }}</p>
                    <p class="text-gray-900 text-lg">{{ $user->email }}</p>
                </div>

                <!-- Third column -->
                <div>
                    <p class="text-gray-500 mb-2">{{ __('messages.mobile') }}</p>
                    <p class="text-gray-900 text-lg">{{ $user->mobile }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                <!-- First column -->
                <div>
                    <p class="text-gray-500 mb-2">{{ __('messages.birthday') }}</p>
                    <p class="text-gray-900 text-lg">{{ $user->dob }}</p>
                </div>

                <!-- Second column -->
                <div>
                    <p class="text-gray-500 mb-2">{{ __('messages.gender') }}</p>
                    <p class="text-gray-900 text-lg">{{ $user->gender }}</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <button id="editProfileBtn"
                    class="bg-gray-900 text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition-colors w-full sm:w-auto">
                    {{ __('messages.edit_profile') }}
                </button>
                <button id="changePasswordBtn"
                    class="bg-gray-900 text-white py-3 px-6 rounded-md font-medium hover:bg-gray-800 transition-colors w-full sm:w-auto">
                    {{ __('messages.change_password') }}
                </button>
            </div>

            <!-- Edit Profile Modal -->
            <div id="editProfileModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-8 w-full max-w-md max-h-[90vh] overflow-y-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.edit_profile') }}</h2>
                        <button class="closeModal text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('front.dashboards.update',[44])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.full_name') }}</label>
                            <input type="text" id="name" name="name" value="{{ $user->name }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-4">
                            <label for="email" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.email_address') }}</label>
                            <input type="email" id="email" readonly value="{{ $user->email }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-4">
                            <label for="mobile" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.mobile') }}</label>
                            <input type="tel" id="mobile" name="mobile" value="{{ $user->mobile }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-4">
                            <label for="dob" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.birthday') }}</label>
                            <input type="date" id="dob" name="dob" value="{{ $user->dob }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-6">
                            <label for="gender" class="block text-gray-700 text-sm font-medium mb-2">{{ __('messages.gender') }}</label>
                            <select id="gender" name="gender"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                                <option value="male" {{ $user->gender =='male' ?'selected':''}}>Male</option>
                                <option value="female" {{ $user->gender =='female' ?'selected':''}}>Female</option>
                                <option value="other" {{ $user->gender =='other' ?'selected':''}}>Other</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-medium mb-2">Profile Image</label>
                            <input type="file" name="image" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    
                            @if($user->image)
                                <img src="{{ asset('images/profile/'.$user->image) }}" 
                                     class="w-20 h-20 rounded-full mt-2 object-cover">
                            @endif
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                class="closeModal px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800">
                                {{ __('messages.save_changes') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password Modal -->
            <div id="changePasswordModal"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg p-8 w-full max-w-md">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-900">{{ __('messages.change_password') }}</h2>
                        <button class="closeModal text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <form action="{{ route('front.passwordUpdate')}}" method="post">
                        @csrf
                        <div class="mb-4">
                            <label for="currentPassword" class="block text-gray-700 text-sm font-medium mb-2">
                                {{ __('messages.current_password') }}
                            </label>
                            <input type="password" id="currentPassword" name="currentPassword"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-4">
                            <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
                                {{ __('messages.new_password') }}
                            </label>
                            <input type="password" id="password" name="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="mb-6">
                            <label for="password_confirmation" class="block text-gray-700 text-sm font-medium mb-2">
                                {{ __('messages.confirm_new_password') }}
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button"
                                class="closeModal px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-100">
                                {{ __('messages.cancel') }}
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-gray-900 text-white rounded-md hover:bg-gray-800">
                                {{ __('messages.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
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

    const editProfileModal = document.getElementById('editProfileModal');
    const changePasswordModal = document.getElementById('changePasswordModal');
    const editProfileBtn = document.getElementById('editProfileBtn');
    const changePasswordBtn = document.getElementById('changePasswordBtn');
    const closeButtons = document.querySelectorAll('.closeModal');

    editProfileBtn.addEventListener('click', () => editProfileModal.classList.remove('hidden'));
    changePasswordBtn.addEventListener('click', () => changePasswordModal.classList.remove('hidden'));

    closeButtons.forEach(button => {
        button.addEventListener('click', () => {
            editProfileModal.classList.add('hidden');
            changePasswordModal.classList.add('hidden');
        });
    });

    window.addEventListener('click', (e) => {
        if (e.target === editProfileModal) editProfileModal.classList.add('hidden');
        if (e.target === changePasswordModal) changePasswordModal.classList.add('hidden');
    });

    
    function closeSuccessAlert() {
        document.getElementById('successAlert').remove();
    }
    
    function closeErrorAlert() {
        document.getElementById('errorAlert').remove();
    }
    
    // Optional: auto close after 2s
    setTimeout(() => {
        const success = document.getElementById('successAlert');
        if(success) success.remove();
    
        const error = document.getElementById('errorAlert');
        if(error) error.remove();
    }, 2000);
</script>
@endpush
