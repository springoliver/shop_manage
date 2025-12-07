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
                        <a href="{{ route('storeowner.employeereviews.review-subjects') }}" class="ml-1 hover:text-gray-700">Review subjects</a>
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
            <!-- Add/Edit Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Review Subjects</h2>
                
                <form action="{{ route('storeowner.employeereviews.update-review-subject') }}" method="POST" id="myform">
                    @csrf
                    @if(old('review_subjectid'))
                        <input type="hidden" name="review_subjectid" id="review_subjectid" value="{{ old('review_subjectid') }}">
                    @endif
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            User Group <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="usergroupid" id="usergroupid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select User Group</option>
                                @foreach($userGroups as $group)
                                    <option value="{{ $group->usergroupid }}" {{ old('usergroupid') == $group->usergroupid ? 'selected' : '' }}>
                                        {{ $group->groupname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Review Subject Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="subject_name" id="subject_name" 
                                   value="{{ old('subject_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.employeereviews.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Existing Subjects Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Review Subjects</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-new">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Review Subject
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    User Group
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($subjects->count() > 0)
                                @foreach($subjects as $subject)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subject->subject_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $subject->groupname ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('storeowner.employeereviews.edit-review-subject', base64_encode($subject->review_subjectid)) }}" 
                                               class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this review subject?')) { document.getElementById('delete-form-{{ $subject->review_subjectid }}').submit(); }"
                                               class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $subject->review_subjectid }}" 
                                                  action="{{ route('storeowner.employeereviews.destroy-review-subject', base64_encode($subject->review_subjectid)) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No records found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 text-sm text-gray-600">
                <strong>Legend(s):</strong>
                <i class="fas fa-edit ml-2"></i> Edit
                <i class="fas fa-trash ml-2"></i> Delete
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

