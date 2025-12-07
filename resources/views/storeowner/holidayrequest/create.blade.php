@section('page_header', 'Add Time Off Request')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.holidayrequest.index') }}" class="ml-1 hover:text-gray-700">Time Off Request</a>
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
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Time Off Request</h1>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('storeowner.holidayrequest.store') }}" method="POST" id="myform">
                @csrf
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Employee <span class="text-red-500">*</span>:
                        </label>
                        <div class="w-3/4">
                            <select name="employeeid" id="employeeid" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Employee</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->employeeid }}">{{ $employee->firstname }} {{ $employee->lastname }}</option>
                                @endforeach
                            </select>
                            @error('employeeid')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            From <span class="text-red-500">*</span>:
                        </label>
                        <div class="w-3/4">
                            <input type="date" name="from_date" id="from_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                            @error('from_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            To <span class="text-red-500">*</span>:
                        </label>
                        <div class="w-3/4">
                            <input type="date" name="to_date" id="to_date" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                            @error('to_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Subject <span class="text-red-500">*</span>:
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="subject" id="subject" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Description <span class="text-red-500">*</span>:
                        </label>
                        <div class="w-3/4">
                            <textarea name="description" id="description" rows="4" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required></textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('storeowner.holidayrequest.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Set minimum date for to_date based on from_date
        document.getElementById('from_date').addEventListener('change', function() {
            const fromDate = this.value;
            const toDateInput = document.getElementById('to_date');
            if (fromDate) {
                toDateInput.min = fromDate;
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

