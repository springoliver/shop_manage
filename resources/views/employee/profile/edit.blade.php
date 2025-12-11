@section('page_header', 'Edit Profile')
<x-employee-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit Profile</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf

                <!-- First Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="firstname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="firstname" id="fname" value="{{ old('firstname', $employee->firstname) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('firstname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Last Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="lastname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="lastname" id="lname" value="{{ old('lastname', $employee->lastname) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('lastname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Username -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="username" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        User Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="uname" id="uname" value="{{ old('uname', $employee->username) }}" onblur="checkusername()" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span id="existunameerror" class="text-red-500 text-sm hidden">Username Already Exist</span>
                        @error('uname')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Email -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="emailid" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="email" name="emailid" id="emailid" value="{{ old('emailid', $employee->emailid) }}" onblur="checkemail()" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span id="existemailerror" class="text-red-500 text-sm hidden">Email Already Exist</span>
                        @error('emailid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="phone" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Phone <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="address" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Address <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="address" id="address" value="{{ old('address', $employee->address1) }}" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Hidden address fields for Google Places API -->
                <div id="details" class="hidden">
                    <input type="hidden" name="address_lat" id="address_lat" data-geo="lat" value="">
                    <input type="hidden" name="address_lng" id="address_lng" data-geo="lng" value="">
                    <input type="hidden" name="address_formatted_address" id="address_formatted_address" data-geo="formatted_address" value="{{ old('address_formatted_address', $employee->address2) }}">
                    <input type="hidden" name="address_street_number" id="address_street_number" data-geo="street_number" value="">
                    <input type="hidden" name="address_street" id="address_street" data-geo="route" value="">
                    <input type="hidden" name="address_airport" id="address_airport" data-geo="code" value="">
                    <input type="hidden" name="address_state" id="address_state" data-geo="administrative_area_level_1" value="{{ old('address_state', $employee->state) }}">
                    <input type="hidden" name="address_country" id="address_country" data-geo="country" value="{{ old('address_country', $employee->country) }}">
                    <input type="hidden" name="address_city" id="address_city" data-geo="locality" value="{{ old('address_city', $employee->city) }}">
                    <input type="hidden" name="address_zipcode" id="address_zipcode" data-geo="postal_code" value="{{ old('address_zipcode', $employee->zipcode) }}">
                    <input type="hidden" name="address_location_type" id="address_location_type" data-geo="location_type" value="">
                </div>

                <!-- Date of Birth -->
                <div class="flex items-start gap-4 mb-6">
                    <label for="dateofbirth" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Date of Birth <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="dateofbirth" id="dateofbirth" value="{{ old('dateofbirth', $employee->dateofbirth ? \Carbon\Carbon::parse($employee->dateofbirth)->format('d-m-Y') : '') }}" autocomplete="off" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" placeholder="dd-mm-yyyy">
                        @error('dateofbirth')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Profile Photo
                    </label>
                    <div class="w-3/4">
                        @if($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo))
                            <img src="{{ Storage::url($employee->profile_photo) }}" alt="Profile Photo" class="h-12 w-12 rounded-full object-cover mb-2">
                        @else
                            <div class="h-12 w-12 bg-gray-200 rounded-full flex items-center justify-center mb-2">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Change Photo? -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Change Photo?
                    </label>
                    <div class="w-3/4">
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="upimg" id="upimgy" value="Yes" class="form-radio">
                            <span class="ml-2">Yes</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="upimg" id="upimgn" value="No" checked class="form-radio">
                            <span class="ml-2">No</span>
                        </label>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="flex items-start gap-4 mb-6 hidden" id="upimagediv">
                    <label for="logo_img" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Image
                    </label>
                    <div class="w-3/4">
                        <input type="file" name="logo_img" id="logo_img" accept="image/jpeg,image/png,image/jpg,image/gif" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="mt-1 text-xs text-gray-500">Allowed: jpeg, png, jpg, gif (max: 2MB)</p>
                        @error('logo_img')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Edit
                    </button>
                    <a href="{{ route('employee.profile.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    @endpush

    @push('scripts')
    <script>
        // Date picker initialization
        flatpickr("#dateofbirth", {
            dateFormat: "d-m-Y",
            maxDate: "today"
        });

        // Show/hide image upload based on radio selection
        document.addEventListener('DOMContentLoaded', function() {
            const upimgYes = document.getElementById('upimgy');
            const upimgNo = document.getElementById('upimgn');
            const upimagediv = document.getElementById('upimagediv');
            const logoImg = document.getElementById('logo_img');

            function toggleImageUpload() {
                if (upimgYes.checked) {
                    upimagediv.classList.remove('hidden');
                    logoImg.setAttribute('required', 'required');
                } else {
                    upimagediv.classList.add('hidden');
                    logoImg.removeAttribute('required');
                    logoImg.value = '';
                }
            }

            upimgYes.addEventListener('change', toggleImageUpload);
            upimgNo.addEventListener('change', toggleImageUpload);

            // Google Places Autocomplete (Vanilla JavaScript)
            initGooglePlacesAutocomplete();
        });

        function initGooglePlacesAutocomplete() {
            // Load Google Maps API dynamically
            if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google.maps_key", "AIzaSyCSmlTnCXc8o9GQZvhXV0NjuZXG57uo1lo") }}&libraries=places&callback=initAutocomplete';
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
                window.initAutocomplete = function() {
                    initializeAutocomplete();
                };
            } else {
                initializeAutocomplete();
            }
        }

        function initializeAutocomplete() {
            const addressInput = document.getElementById('address');
            if (!addressInput) return;

            const autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['geocode', 'establishment']
            });

            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                
                if (!place.geometry) {
                    console.warn('No details available for input: ' + place.name);
                    return;
                }

                // Populate hidden fields based on place details
                for (let i = 0; i < place.address_components.length; i++) {
                    const component = place.address_components[i];
                    const componentType = component.types[0];

                    // Map address components to hidden fields
                    const fieldMap = {
                        'street_number': 'address_street_number',
                        'route': 'address_street',
                        'locality': 'address_city',
                        'administrative_area_level_1': 'address_state',
                        'country': 'address_country',
                        'postal_code': 'address_zipcode'
                    };

                    if (fieldMap[componentType]) {
                        const field = document.getElementById(fieldMap[componentType]);
                        if (field) {
                            field.value = component.long_name;
                        }
                    }
                }

                // Set coordinates
                const latField = document.getElementById('address_lat');
                const lngField = document.getElementById('address_lng');
                if (latField) latField.value = place.geometry.location.lat();
                if (lngField) lngField.value = place.geometry.location.lng();

                // Set formatted address
                const formattedField = document.getElementById('address_formatted_address');
                if (formattedField) formattedField.value = place.formatted_address;

                // Set location type
                const locationTypeField = document.getElementById('address_location_type');
                if (locationTypeField) locationTypeField.value = place.geometry.location_type || '';

                // Set airport code if available (not standard, but keeping for compatibility)
                const airportField = document.getElementById('address_airport');
                if (airportField) {
                    // This is not a standard Places API field, leaving empty
                    airportField.value = '';
                }
            });
        }

        // Check username uniqueness
        function checkusername() {
            const username = document.getElementById('uname').value;
            const employeeid = {{ $employee->employeeid }};
            
            if (!username) {
                document.getElementById('existunameerror').classList.add('hidden');
                return;
            }

            fetch('{{ route('employee.profile.check-username') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ username: username })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('existunameerror').classList.remove('hidden');
                } else {
                    document.getElementById('existunameerror').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Check email uniqueness
        function checkemail() {
            const email = document.getElementById('emailid').value;
            const employeeid = {{ $employee->employeeid }};
            
            if (!email) {
                document.getElementById('existemailerror').classList.add('hidden');
                return;
            }

            fetch('{{ route('employee.profile.check-email') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email: email })
            })
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    document.getElementById('existemailerror').classList.remove('hidden');
                } else {
                    document.getElementById('existemailerror').classList.add('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Form validation
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            const upimgYes = document.getElementById('upimgy');
            const logoImg = document.getElementById('logo_img');
            
            if (upimgYes.checked && !logoImg.files.length) {
                e.preventDefault();
                alert('Please select an image file.');
                return false;
            }

            // Validate file type if image is selected
            if (upimgYes.checked && logoImg.files.length) {
                const file = logoImg.files[0];
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    alert('Please select only jpeg, png, jpg, or gif image files.');
                    return false;
                }
            }
        });
    </script>
    @endpush
</x-employee-app-layout>
