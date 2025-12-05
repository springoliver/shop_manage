@section('page_header', 'Add Department')

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
                        <a href="{{ route('admin.departments.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Departments</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add Department</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Department</h1>
    </div>

    <!-- Note about mandatory fields -->
    <div class="mb-6 text-right">
        <p class="text-sm text-gray-600">All fields marked with (<span class="text-red-500">*</span>) are mandatory.</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <!-- Card Header -->
        <div class="bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center">
                <i class="fas fa-bars text-gray-600 mr-3"></i>
                <h3 class="text-lg font-semibold text-gray-800">Department Information</h3>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('admin.departments.store') }}" method="POST" id="departmentForm">
                @csrf

                <!-- Store Type -->
                <div class="mb-6">
                    <label for="storetypeid" class="block text-sm font-medium text-gray-700 mb-2">
                        Store Type <span class="text-red-500">*</span>
                    </label>
                    <select name="storetypeid" id="storetypeid"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('storetypeid') border-red-500 @enderror"
                        required>
                        <option value="">Select Store Type</option>
                        @foreach($storeTypes as $storeType)
                            <option value="{{ $storeType->typeid }}" {{ old('storetypeid') == $storeType->typeid ? 'selected' : '' }}>
                                {{ $storeType->store_type }}
                            </option>
                        @endforeach
                    </select>
                    @error('storetypeid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Store -->
                <div class="mb-6">
                    <label for="storeid" class="block text-sm font-medium text-gray-700 mb-2">
                        Store <span class="text-red-500">*</span>
                    </label>
                    <select name="storeid" id="storeid"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('storeid') border-red-500 @enderror"
                        required>
                        <option value="">Select Store</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->storeid }}" {{ old('storeid') == $store->storeid ? 'selected' : '' }}>
                                {{ $store->store_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('storeid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Department Name -->
                <div class="mb-6">
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="department" id="department" value="{{ old('department') }}" maxlength="255"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('department') border-red-500 @enderror"
                        required onblur="checkDepartmentName(this.value)">
                    <span id="existdepterror" class="text-red-600 text-sm mt-1" style="display:none">Department Name Already Exist</span>
                    @error('department')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Roster Max Time -->
                <div class="mb-6">
                    <label for="roster_max_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Roster Max Time
                    </label>
                    <input type="number" name="roster_max_time" id="roster_max_time" value="{{ old('roster_max_time', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('roster_max_time') border-red-500 @enderror">
                    @error('roster_max_time')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-start space-x-4">
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                    <a href="{{ route('admin.departments.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkDepartmentName(department) {
            if (!department) return;
            
            fetch('{{ route("admin.departments.check-department") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    department: department
                })
            })
            .then(response => response.json())
            .then(data => {
                const errorSpan = document.getElementById('existdepterror');
                if (data == 1) {
                    errorSpan.style.display = 'block';
                } else {
                    errorSpan.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
    @endpush
</x-admin-app-layout>

