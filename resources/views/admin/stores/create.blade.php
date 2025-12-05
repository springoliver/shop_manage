@section('page_header', 'Create Store')

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
                        <a href="{{ route('admin.stores.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Store</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Create</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Store</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('admin.stores.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Store Information Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-store mr-2"></i>
                    Add Store
                </h3>

                <div class="grid grid-cols-1 gap-6">
                    <!-- Store Owner Name -->
                    <div>
                        <label for="storeownerid" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Owner Name <span class="text-red-500">*</span>
                        </label>
                        <select name="storeownerid" id="storeownerid"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('storeownerid') border-red-500 @enderror"
                            required>
                            <option value="">Select Store Owner</option>
                            @foreach($storeOwners as $owner)
                                <option value="{{ $owner->ownerid }}" {{ old('storeownerid') == $owner->ownerid ? 'selected' : '' }}>
                                    {{ $owner->firstname }} {{ $owner->lastname }}
                                </option>
                            @endforeach
                        </select>
                        @error('storeownerid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Name -->
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="store_name" id="store_name" value="{{ old('store_name') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('store_name') border-red-500 @enderror"
                            required>
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Category -->
                    <div>
                        <label for="typeid" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Category <span class="text-red-500">*</span>
                        </label>
                        <select name="typeid" id="typeid"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('typeid') border-red-500 @enderror"
                            required>
                            <option value="">Select a Category</option>
                            @foreach($storeTypes as $type)
                                <option value="{{ $type->typeid }}" {{ old('typeid') == $type->typeid ? 'selected' : '' }}>
                                    {{ $type->store_type }}
                                </option>
                            @endforeach
                        </select>
                        @error('typeid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website URL -->
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Website URL
                        </label>
                        <input type="url" name="website_url" id="website_url" value="{{ old('website_url') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500"
                            placeholder="https://example.com">
                        @error('website_url')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Store Email -->
                    <div>
                        <label for="store_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Store Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" name="store_email" id="store_email" value="{{ old('store_email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('store_email') border-red-500 @enderror"
                            required>
                        @error('store_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Manager Email -->
                    <div>
                        <label for="manager_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Manager Email
                        </label>
                        <input type="email" name="manager_email" id="manager_email" value="{{ old('manager_email') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                        @error('manager_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="full_google_address" class="block text-sm font-medium text-gray-700 mb-2">
                            Address
                        </label>
                        <input type="text" name="full_google_address" id="full_google_address" value="{{ old('full_google_address') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500"
                            placeholder="Enter full address">
                        @error('full_google_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Latitude & Longitude -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Latitude
                            </label>
                            <input type="text" name="latitude" id="latitude" value="{{ old('latitude') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500"
                                placeholder="e.g., 40.7128">
                        </div>
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                Longitude
                            </label>
                            <input type="text" name="longitude" id="longitude" value="{{ old('longitude') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500"
                                placeholder="e.g., -74.0060">
                        </div>
                    </div>

                    <!-- Logo Image -->
                    <div>
                        <label for="logofile" class="block text-sm font-medium text-gray-700 mb-2">
                            Logo Image <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <img id="logo-preview" src="https://via.placeholder.com/150" alt="Logo Preview" class="w-32 h-32 object-cover border-2 border-gray-300 rounded">
                            </div>
                            <div class="flex-1">
                                <input type="file" name="logofile" id="logofile" accept="image/*"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('logofile') border-red-500 @enderror"
                                    onchange="previewLogo(event)">
                                <p class="mt-1 text-sm text-gray-500">Accepted formats: JPG, PNG, GIF (Max: 2MB)</p>
                                @error('logofile')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Hours Section -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Store Hours</h3>

                @php
                    $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                @endphp

                @foreach($days as $day)
                    <div class="mb-4 p-4 border border-gray-200 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-2 capitalize">
                                    {{ ucfirst($day) }}
                                </label>
                            </div>
                            <div>
                                <label for="{{ $day }}_hour_from" class="block text-xs text-gray-600 mb-1">
                                    From
                                </label>
                                <input type="time" name="{{ $day }}_hour_from" id="{{ $day }}_hour_from"
                                    value="{{ old($day.'_hour_from') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label for="{{ $day }}_hour_to" class="block text-xs text-gray-600 mb-1">
                                    To
                                </label>
                                <input type="time" name="{{ $day }}_hour_to" id="{{ $day }}_hour_to"
                                    value="{{ old($day.'_hour_to') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-gray-500 focus:border-gray-500">
                            </div>
                            <div>
                                <label for="{{ $day }}_dayoff" class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="{{ $day }}_dayoff" id="{{ $day }}_dayoff"
                                        value="Yes" {{ old($day.'_dayoff') == 'Yes' ? 'checked' : '' }}
                                        class="w-4 h-4 text-gray-600 border-gray-300 rounded focus:ring-gray-500">
                                    <span class="ml-2 text-sm text-gray-700">Day Off</span>
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Status -->
            <div class="mb-6">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>
                <select name="status" id="status"
                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                    <option value="Pending Setup" {{ old('status') === 'Pending Setup' ? 'selected' : '' }}>Pending Setup</option>
                    <option value="Active" {{ old('status') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Suspended" {{ old('status') === 'Suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="Closed" {{ old('status') === 'Closed' ? 'selected' : '' }}>Closed</option>
                </select>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-start space-x-4 pt-6 border-t border-gray-200">
                <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Create
                </button>
                <a href="{{ route('admin.stores.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function previewLogo(event) {
            const preview = document.getElementById('logo-preview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
    @endpush
</x-admin-app-layout>

