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
                        <a href="{{ route('storeowner.employeereviews.index') }}" class="ml-1 hover:text-gray-700">Employees</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
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

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    Add Review for {{ ucfirst($employee->firstname) }} {{ ucfirst($employee->lastname) }}
                </h2>
                
                <form action="{{ route('storeowner.employeereviews.insert-review') }}" method="POST" id="myform">
                    @csrf
                    <input type="hidden" name="employeeid" id="employeeid" value="{{ base64_encode($employee->employeeid) }}" />
                    
                    @if($reviewSubjects->count() > 0)
                        <div class="overflow-x-auto mb-6">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Review Subject</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comment</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reviewSubjects as $subject)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $subject->subject_name }}
                                                <input type="hidden" name="review_subjectid[]" value="{{ $subject->review_subjectid }}" />
                                            </td>
                                            <td class="px-6 py-4">
                                                <textarea name="comments[]" rows="3" 
                                                          class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"></textarea>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                            No review subjects found for this employee's user group. Please add review subjects first.
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            NEXT REVIEW DATE <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="next_review_date" id="next_review_date" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                               required>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700" {{ $reviewSubjects->count() == 0 ? 'disabled' : '' }}>
                            Save
                        </button>
                        <a href="{{ route('storeowner.employeereviews.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

