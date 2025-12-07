<x-guest-layout>
    <div class="w-full max-w-2xl bg-white/95 rounded-lg shadow-lg p-8">
        <h1 class="text-center mb-6 font-medium text-xl">Store Registration</h1>
        
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('storeowner.register.store') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <input type="hidden" name="ownerid" value="{{ $ownerid }}">

            <!-- Store Name -->
            <div class="flex items-start gap-4">
                <label for="store_name" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Store Name<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="store_name" class="block w-full" type="text" name="store_name" :value="old('store_name')" placeholder="Enter Store Name." required autofocus />
                    <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
                </div>
            </div>

            <!-- Store Type -->
            <div class="flex items-start gap-4">
                <label for="typeid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Store Type<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <select id="typeid" name="typeid" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Select Store Type</option>
                        @foreach($storeTypes as $storeType)
                            <option value="{{ $storeType->typeid }}" {{ old('typeid') == $storeType->typeid ? 'selected' : '' }}>
                                {{ $storeType->store_type }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('typeid')" class="mt-2" />
                </div>
            </div>

            <!-- Website URL -->
            <div class="flex items-start gap-4">
                <label for="weburl" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Website URL<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="weburl" class="block w-full" type="url" name="weburl" :value="old('weburl')" placeholder="Enter Website URL." required />
                    <x-input-error :messages="$errors->get('weburl')" class="mt-2" />
                </div>
            </div>

            <!-- Store Email -->
            <div class="flex items-start gap-4">
                <label for="store_email" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Store Email<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="store_email" class="block w-full" type="email" name="store_email" :value="old('store_email')" placeholder="Enter Store Email." required />
                    <x-input-error :messages="$errors->get('store_email')" class="mt-2" />
                </div>
            </div>

            <!-- Store Address -->
            <div class="flex items-start gap-4">
                <label for="address1" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Store Address<span class="text-red-500"> *</span>
                </label>
                <div class="w-3/4">
                    <x-text-input id="address1" class="block w-full" type="text" name="address1" placeholder="Location" :value="old('address1')" required />
                    <x-input-error :messages="$errors->get('address1')" class="mt-2" />
                </div>
            </div>

            <!-- Store Logo -->
            <div class="flex items-start gap-4">
                <label for="logo_img" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Store Logo
                </label>
                <div class="w-3/4">
                    <input id="logo_img" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500" type="file" name="logo_img" accept="image/jpeg,image/png,image/jpg,image/gif" />
                    <x-input-error :messages="$errors->get('logo_img')" class="mt-2" />
                </div>
            </div>

            <!-- Hidden fields for Google Places API -->
            <div style="display: none;">
                <input type="hidden" name="address_lat1" id="address_lat1" value="{{ old('address_lat1') }}">
                <input type="hidden" name="address_lng1" id="address_lng1" value="{{ old('address_lng1') }}">
                <input type="hidden" name="address_formatted_address1" id="address_formatted_address1" value="{{ old('address_formatted_address1') }}">
                <input type="hidden" name="address_street_number1" id="address_street_number1" value="{{ old('address_street_number1') }}">
                <input type="hidden" name="address_street1" id="address_street1" value="{{ old('address_street1') }}">
                <input type="hidden" name="address_airport1" id="address_airport1" value="{{ old('address_airport1') }}">
                <input type="hidden" name="address_state1" id="address_state1" value="{{ old('address_state1') }}">
                <input type="hidden" name="address_country1" id="address_country1" value="{{ old('address_country1') }}">
                <input type="hidden" name="address_city1" id="address_city1" value="{{ old('address_city1') }}">
                <input type="hidden" name="address_zipcode1" id="address_zipcode1" value="{{ old('address_zipcode1') }}">
                <input type="hidden" name="address_location_type1" id="address_location_type1" value="{{ old('address_location_type1') }}">
            </div>

            <div class="flex items-center justify-end mt-6">
                <x-primary-button>
                    Register
                </x-primary-button>
            </div>
        </form>
    </div>
</x-guest-layout>

