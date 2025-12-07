@section('page_header', 'Add Department')

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
                        <a href="{{ route('storeowner.department.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Departments</a>
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
        <h1 class="text-2xl font-semibold text-gray-800">Add Department</h1>
        <p class="text-sm text-gray-500 mt-1">All fields marked with * are compulsory</p>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('storeowner.department.store') }}" method="POST" id="departmentForm" onsubmit="return validateAndSubmit(event)">
                @csrf

                <!-- Store Type -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Store Type <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <select name="storeid" id="storeid" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            @foreach($storeTypes as $storeType)
                                <option value="{{ $storeType->typeid }}" {{ ($currentStoreType && $currentStoreType->typeid == $storeType->typeid) ? 'selected' : '' }}>
                                    {{ $storeType->store_type }}
                                </option>
                            @endforeach
                        </select>
                        @error('storeid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Department Name -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Department Name <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="text" name="department" id="department" value="{{ old('department') }}" maxlength="30" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        <span id="existdepterror" class="text-red-500 text-sm hidden">Department Name Already Exist</span>
                        @error('department')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Weekly Maximum Roster Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Weekly Maximum Roster Hours <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="wroster" id="wroster" value="{{ old('wroster') }}" maxlength="10" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('wroster')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Daily Target Labour Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Daily Target Labour Hours <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="target_hours" id="target_hours" value="{{ old('target_hours') }}" maxlength="10" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('target_hours')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Maximum Shift Hour in One day -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Enter maximum Shift Hour in One day for each employee <span class="text-red-500">*</span>
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="droster" id="droster" value="{{ old('droster') }}" maxlength="10" required class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        @error('droster')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Monday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Monday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Monday" id="Monday" value="{{ old('Monday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Tuesday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Tuesday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Tuesday" id="Tuesday" value="{{ old('Tuesday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Wednesday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Wednesday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Wednesday" id="Wednesday" value="{{ old('Wednesday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Thursday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Thursday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Thursday" id="Thursday" value="{{ old('Thursday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Friday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Friday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Friday" id="Friday" value="{{ old('Friday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Saturday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Saturday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Saturday" id="Saturday" value="{{ old('Saturday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Sunday Hours -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Sunday Hours
                    </label>
                    <div class="w-3/4">
                        <input type="number" name="Sunday" id="Sunday" value="{{ old('Sunday', 0) }}" maxlength="10" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end gap-4 pt-6 border-t">
                    <a href="{{ route('storeowner.department.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-storeowner-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('existdepterror').style.display = 'none';
    });

    // Make functions globally accessible
    window.validateAndSubmit = function(event) {
        if (event) {
            event.preventDefault();
        }
        
        var form = document.getElementById('departmentForm');
        
        // Check HTML5 validation first
        if (!form.checkValidity()) {
            form.reportValidity();
            return false;
        }
        
        // If validation passes, check department name
        checkDepartmentName();
        return false;
    };

    window.checkDepartmentName = function() {
        var department = document.getElementById('department').value;
        
        if (!department || department.trim() === '') {
            document.getElementById('departmentForm').submit();
            return;
        }
        
        var postData = {
            'department': department,
            '_token': '{{ csrf_token() }}'
        };

        fetch('{{ route('storeowner.department.check-name') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'text/plain'
            },
            body: JSON.stringify(postData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            if (data.trim() == '1') {
                document.getElementById('existdepterror').classList.remove('hidden');
                document.getElementById('department').focus();
            } else {
                document.getElementById('existdepterror').classList.add('hidden');
                // Remove the onsubmit handler temporarily to allow normal submission
                document.getElementById('departmentForm').onsubmit = null;
                document.getElementById('departmentForm').submit();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // If there's an error, still submit the form (server will validate)
            document.getElementById('departmentForm').onsubmit = null;
            document.getElementById('departmentForm').submit();
        });
    };
</script>
@endpush

