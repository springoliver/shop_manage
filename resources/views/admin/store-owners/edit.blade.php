@section('page_header', 'Edit Store Owner')

<x-admin-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('admin.store-owners.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Store Owner</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Store Owner</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.store-owners.update', $storeOwner) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Personal Information Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-user mr-2"></i>
                    Edit Store Owner
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- First Name -->
                    <div>
                        <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">
                            First Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="firstname" id="firstname" value="{{ old('firstname', $storeOwner->firstname) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('firstname') border-red-500 @enderror"
                            required>
                        @error('firstname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Last Name -->
                    <div>
                        <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">
                            Last Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lastname" id="lastname" value="{{ old('lastname', $storeOwner->lastname) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('lastname') border-red-500 @enderror"
                            required>
                        @error('lastname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- User Name -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            User Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="username" id="username" value="{{ old('username', $storeOwner->username) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('username') border-red-500 @enderror"
                            required>
                        @error('username')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="emailid" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="emailid" id="emailid" value="{{ old('emailid', $storeOwner->emailid) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('emailid') border-red-500 @enderror"
                            required>
                        @error('emailid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Phone <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $storeOwner->phone) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('phone') border-red-500 @enderror"
                            required>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date of Birth -->
                    <div>
                        <label for="dateofbirth" class="block text-sm font-medium text-gray-700 mb-2">
                            Date of birth <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="dateofbirth" id="dateofbirth" value="{{ old('dateofbirth', $storeOwner->dateofbirth->format('Y-m-d')) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('dateofbirth') border-red-500 @enderror"
                            required>
                        @error('dateofbirth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="mb-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Address 1 -->
                    <div>
                        <label for="address1" class="block text-sm font-medium text-gray-700 mb-2">
                            Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="address1" id="address1" value="{{ old('address1', $storeOwner->address1) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('address1') border-red-500 @enderror"
                            required>
                        @error('address1')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address 2 -->
                    <div>
                        <label for="address2" class="block text-sm font-medium text-gray-700 mb-2">
                            Address 2
                        </label>
                        <input type="text" name="address2" id="address2" value="{{ old('address2', $storeOwner->address2) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" id="city" value="{{ old('city', $storeOwner->city) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('city') border-red-500 @enderror"
                                required>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">
                                State <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="state" id="state" value="{{ old('state', $storeOwner->state) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('state') border-red-500 @enderror"
                                required>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Zipcode -->
                        <div>
                            <label for="zipcode" class="block text-sm font-medium text-gray-700 mb-2">
                                Zip Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="zipcode" id="zipcode" value="{{ old('zipcode', $storeOwner->zipcode) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('zipcode') border-red-500 @enderror"
                                required>
                            @error('zipcode')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="country" id="country" value="{{ old('country', $storeOwner->country) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('country') border-red-500 @enderror"
                            required>
                        @error('country')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Profile Image Section -->
            <div class="mb-6">
                <label for="profile_photo" class="block text-sm font-medium text-gray-700 mb-2">
                    Profile Image <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        @if($storeOwner->profile_photo)
                            <img id="profile-preview" src="{{ asset('storage/' . $storeOwner->profile_photo) }}" alt="Profile Preview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                        @else
                            <img id="profile-preview" src="https://via.placeholder.com/150" alt="Profile Preview" class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                        @endif
                    </div>
                    <div class="flex-1">
                        <div class="mb-2">
                            <label class="text-sm font-medium text-gray-700">Do you want to update image?</label>
                            <div class="flex items-center space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="update_image" value="yes" class="form-radio text-blue-600" onchange="toggleImageUpload(true)">
                                    <span class="ml-2">Yes</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="update_image" value="no" class="form-radio text-blue-600" checked onchange="toggleImageUpload(false)">
                                    <span class="ml-2">No</span>
                                </label>
                            </div>
                        </div>
                        <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('profile_photo') border-red-500 @enderror"
                            onchange="previewImage(event)" disabled>
                        <p class="mt-1 text-sm text-gray-500">Accepted formats: JPG, PNG, GIF (Max: 2MB)</p>
                        @error('profile_photo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Account Security Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Update Password (Leave blank to keep current)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            New Password
                        </label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-start space-x-4 pt-6 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Update
                </button>
                <a href="{{ route('admin.store-owners.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewImage(event) {
            const preview = document.getElementById('profile-preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }

        function toggleImageUpload(enable) {
            const fileInput = document.getElementById('profile_photo');
            fileInput.disabled = !enable;
            if (!enable) {
                fileInput.value = '';
            }
        }
    </script>
    @endpush
</x-admin-app-layout>

