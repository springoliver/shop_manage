@section('page_header', 'Time Off Request')
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
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('employee.holidayrequest.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Time Off Request</a>
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

    <!-- Flash Messages -->
    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('employee.holidayrequest.store') }}" method="POST" id="myform">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="from_date" class="block text-sm font-medium text-gray-700 mb-2">
                            From <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="from_date" 
                               id="from_date" 
                               value="{{ old('from_date') }}"
                               autocomplete="off"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        @error('from_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="to_date" class="block text-sm font-medium text-gray-700 mb-2">
                            To <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="to_date" 
                               id="to_date" 
                               value="{{ old('to_date') }}"
                               autocomplete="off"
                               required
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        @error('to_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                            Subject <span class="text-red-600">*</span>
                        </label>
                        <input type="text" 
                               name="subject" 
                               id="subject" 
                               value="{{ old('subject') }}"
                               required
                               maxlength="255"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        @error('subject')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-600">*</span>
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="4"
                                  required
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end space-x-3">
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Save
                    </button>
                    <a href="{{ route('employee.holidayrequest.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("#from_date", {
                dateFormat: "d-m-Y",
                allowInput: true,
            });

            flatpickr("#to_date", {
                dateFormat: "d-m-Y",
                allowInput: true,
            });

            // Form validation
            const form = document.getElementById('myform');
            form.addEventListener('submit', function(e) {
                const fromDate = document.getElementById('from_date').value;
                const toDate = document.getElementById('to_date').value;
                const subject = document.getElementById('subject').value;

                if (!fromDate || !toDate || !subject) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return false;
                }

                // Validate date format (dd-mm-yyyy)
                const dateRegex = /^\d{2}-\d{2}-\d{4}$/;
                if (!dateRegex.test(fromDate) || !dateRegex.test(toDate)) {
                    e.preventDefault();
                    alert('Please enter dates in dd-mm-yyyy format.');
                    return false;
                }

                // Validate to_date is after or equal to from_date
                const fromParts = fromDate.split('-');
                const toParts = toDate.split('-');
                const fromDateObj = new Date(fromParts[2], fromParts[1] - 1, fromParts[0]);
                const toDateObj = new Date(toParts[2], toParts[1] - 1, toParts[0]);

                if (toDateObj < fromDateObj) {
                    e.preventDefault();
                    alert('To date must be after or equal to From date.');
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-employee-app-layout>

