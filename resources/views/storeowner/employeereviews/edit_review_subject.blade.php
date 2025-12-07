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
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Edit Review Subject</h2>
                
                <form action="{{ route('storeowner.employeereviews.update-review-subject') }}" method="POST" id="myform">
                    @csrf
                    <input type="hidden" name="review_subjectid" value="{{ base64_encode($subject->review_subjectid) }}">
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            User Group <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="usergroupid" id="usergroupid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select User Group</option>
                                @foreach($userGroups as $group)
                                    <option value="{{ $group->usergroupid }}" {{ $subject->usergroupid == $group->usergroupid ? 'selected' : '' }}>
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
                                   value="{{ $subject->subject_name }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.employeereviews.add-review-subject') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

