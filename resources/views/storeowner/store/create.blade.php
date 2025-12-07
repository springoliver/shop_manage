@section('page_header', 'Add Store')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.store.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">My Store</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Store</h1>
    </div>

    <form method="POST" action="{{ route('storeowner.store.store') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- Store Type -->
        <div class="flex items-start gap-4">
            <label for="typeid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                Store Type<span class="text-red-500"> *</span>
            </label>
            <div class="w-3/4">
                <select id="typeid" name="typeid" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                    <option value="">Select Type</option>
                    @foreach($storeTypes as $storeType)
                        <option value="{{ $storeType->typeid }}" {{ old('typeid') == $storeType->typeid ? 'selected' : '' }}>
                            {{ $storeType->store_type }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('typeid')" class="mt-2" />
            </div>
        </div>

        <!-- Store Name -->
        <div class="flex items-start gap-4">
            <label for="store_name" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                Store Name<span class="text-red-500"> *</span>
            </label>
            <div class="w-3/4" id="storename">
                <x-text-input id="store_name" class="block w-full" type="text" name="store_name" :value="old('store_name')" required onblur="checkstorename()" />
                <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
            </div>
        </div>

        <!-- User Group -->
        <div class="flex items-start gap-4">
            <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                User Group
            </label>
            <div class="w-3/4">
                @foreach($userGroups as $userGroup)
                    <label class="flex items-center mb-2">
                        <input type="checkbox" name="groupname[]" value="{{ $userGroup->usergroupid }}" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-700">{{ $userGroup->groupname }}</span>
                    </label>
                @endforeach
                <x-input-error :messages="$errors->get('groupname')" class="mt-2" />
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
                Store Gmail<span class="text-red-500"> *</span>
            </label>
            <div class="w-3/4">
                <x-text-input id="store_email" class="block w-full" type="email" name="store_email" :value="old('store_email')" placeholder="Enter Store Email." required onblur="checkstoreemail()" />
                <span id="storeemailerror" style="color:red; display: none;">Please Enter Gmail Email</span>
                <span id="existstoreemailerror" style="color:red; display: none;">Email already exist</span>
                <x-input-error :messages="$errors->get('store_email')" class="mt-2" />
            </div>
        </div>

        <!-- Gmail Password -->
        <div class="flex items-start gap-4">
            <label for="store_email_pass" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                GMail Password
            </label>
            <div class="w-3/4">
                <x-text-input id="store_email_pass" class="block w-full" type="password" name="store_email_pass" />
                <x-input-error :messages="$errors->get('store_email_pass')" class="mt-2" />
            </div>
        </div>

        <!-- Manager Email -->
        <div class="flex items-start gap-4">
            <label for="manager_email" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                Manager Email<span class="text-red-500"> *</span>
            </label>
            <div class="w-3/4">
                <x-text-input id="manager_email" class="block w-full" type="email" name="manager_email" :value="old('manager_email')" placeholder="Enter Manager Email." required />
                <x-input-error :messages="$errors->get('manager_email')" class="mt-2" />
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

        <!-- Hidden fields for Google Places API -->
        <div style="display: none;">
            <input type="hidden" name="address_lat" id="address_lat" value="{{ old('address_lat') }}">
            <input type="hidden" name="address_lng" id="address_lng" value="{{ old('address_lng') }}">
            <input type="hidden" name="address_formatted_address" id="address_formatted_address" value="{{ old('address_formatted_address') }}">
            <input type="hidden" name="address_street_number" id="address_street_number" value="{{ old('address_street_number') }}">
            <input type="hidden" name="address_street" id="address_street" value="{{ old('address_street') }}">
            <input type="hidden" name="address_airport" id="address_airport" value="{{ old('address_airport') }}">
            <input type="hidden" name="address_state" id="address_state" value="{{ old('address_state') }}">
            <input type="hidden" name="address_country" id="address_country" value="{{ old('address_country') }}">
            <input type="hidden" name="address_city" id="address_city" value="{{ old('address_city') }}">
            <input type="hidden" name="address_zipcode" id="address_zipcode" value="{{ old('address_zipcode') }}">
            <input type="hidden" name="address_location_type" id="address_location_type" value="{{ old('address_location_type') }}">
        </div>

        <!-- Logo Photo -->
        <div class="flex items-start gap-4">
            <label for="logo_img" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                Logo Photo
            </label>
            <div class="w-3/4">
                <input id="logo_img" class="block w-full border border-gray-300 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500" type="file" name="logo_img" accept="image/jpeg,image/png,image/jpg,image/gif" />
                <x-input-error :messages="$errors->get('logo_img')" class="mt-2" />
            </div>
        </div>

        <!-- Store Opening Times -->
        <div class="flex items-start gap-4">
            <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                Store Opening Times
            </label>
            <div class="w-3/4">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Day of Week</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Is Day off</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                                $dayLabels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                                $timeOptions = [];
                                for($i = 1; $i <= 24; $i++) {
                                    $timeOptions[] = $i . ':00';
                                    if($i != 24) {
                                        $timeOptions[] = $i . ':30';
                                    } else {
                                        $timeOptions[] = '24:00';
                                    }
                                }
                            @endphp
                            @foreach($days as $index => $day)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $dayLabels[$index] }}</td>
                                    <td class="px-4 py-2">
                                        <select name="{{ $day }}_hour_from" id="{{ $day }}_hour_from" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}" {{ old($day . '_hour_from', '9:00') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <select name="{{ $day }}_hour_to" id="{{ $day }}_hour_to" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}" {{ old($day . '_hour_to', '17:00') == $time ? 'selected' : '' }}>{{ $time }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2">
                                        <div class="flex items-center space-x-4">
                                            <label class="flex items-center">
                                                <input type="radio" name="{{ $day }}_dayoff" value="Yes" class="mr-1" {{ old($day . '_dayoff', 'No') == 'Yes' ? 'checked' : '' }}>
                                                <span class="text-sm">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" name="{{ $day }}_dayoff" value="No" class="mr-1" {{ old($day . '_dayoff', 'No') == 'No' ? 'checked' : '' }}>
                                                <span class="text-sm">No</span>
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('storeowner.store.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Cancel
            </a>
            <x-primary-button>
                Save
            </x-primary-button>
        </div>
    </form>

    @push('scripts')
    <script>
        function checkstorename() {
            var store_name = document.getElementById('store_name').value;
            if (!store_name) return;
            
            fetch('{{ route('storeowner.store.check-name') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ store_name: store_name })
            })
            .then(response => response.json())
            .then(data => {
                var storeNameInput = document.getElementById('store_name');
                var storeNameDiv = document.getElementById('storename');
                var errorSpan = storeNameDiv.querySelector('span.error-message');
                
                if(data.message == "invalid"){
                    storeNameInput.classList.remove('border-gray-300');
                    storeNameInput.classList.add('border-red-500');
                    if (!errorSpan) {
                        var span = document.createElement('span');
                        span.className = 'error-message';
                        span.style.color = 'red';
                        span.className += ' block mt-1 text-sm';
                        span.textContent = 'Store name already exists.';
                        storeNameDiv.appendChild(span);
                    }
                } else {
                    storeNameInput.classList.remove('border-red-500');
                    storeNameInput.classList.add('border-gray-300');
                    if (errorSpan) {
                        errorSpan.remove();
                    }
                }
            });
        }

        function checkstoreemail() {
            var email = document.getElementById('store_email').value;
            if (!email) return;
            
            fetch('{{ route('storeowner.store.check-email') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.text())
            .then(msg => {
                var existError = document.getElementById('existstoreemailerror');
                var emailError = document.getElementById('storeemailerror');
                
                if (msg == 'valid') {
                    if (existError) existError.style.display = 'none';
                    if (emailError) emailError.style.display = 'none';
                } else {
                    if(email == ''){
                        if (existError) existError.style.display = 'none';
                        if (emailError) emailError.style.display = 'block';
                    } else {
                        if (existError) existError.style.display = 'block';
                        if (emailError) emailError.style.display = 'none';
                    }
                }
            });
        }
    </script>
    @endpush
</x-storeowner-app-layout>

