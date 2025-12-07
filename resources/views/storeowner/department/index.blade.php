@section('page_header', 'Departments')

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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Departments</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Departments</h1>
        <a href="{{ route('storeowner.department.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
            <i class="fas fa-plus mr-2"></i>
            Add
        </a>
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

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Department Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Store Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($departments as $department)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $department->department }}
                                        @if($department->storeid != '0')
                                            <span class="text-gray-500 text-xs">(Custom)</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $department->store_type ?? 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($department->storeid != '0')
                                        @if($department->status == 'Enable')
                                            <button type="button" onclick="window.openStatusModal('confirm-status{{ $department->departmentid }}')" class="px-3 py-1 bg-green-500 text-white text-xs font-semibold rounded hover:bg-green-600">
                                                Enable
                                            </button>
                                        @else
                                            <button type="button" onclick="window.openStatusModal('confirm-status{{ $department->departmentid }}')" class="px-3 py-1 bg-red-500 text-white text-xs font-semibold rounded hover:bg-red-600">
                                                Disable
                                            </button>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    @if($department->storeid != '0')
                                        <div class="flex justify-center space-x-3">
                                            <!-- Edit -->
                                            <a href="{{ route('storeowner.department.edit', $department->departmentid) }}" class="text-gray-600 hover:text-gray-900" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <!-- Delete -->
                                            <form action="{{ route('storeowner.department.destroy', $department->departmentid) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this department?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No departments found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Status Change Modals -->
    @foreach ($departments as $department)
        @if($department->storeid != '0')
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="confirm-status{{ $department->departmentid }}" onclick="if(event.target === this) window.closeStatusModal('confirm-status{{ $department->departmentid }}')">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" onclick="event.stopPropagation()">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Change Status</h3>
                            <button onclick="window.closeStatusModal('confirm-status{{ $department->departmentid }}')" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <form action="{{ route('storeowner.department.change-status') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="Enable" {{ $department->status == 'Enable' ? 'checked' : '' }} class="mr-2">
                                        <span>Enable</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="status" value="Disable" {{ $department->status == 'Disable' ? 'checked' : '' }} class="mr-2">
                                        <span>Disable</span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex items-center justify-end space-x-3">
                                <input type="hidden" name="departmentid" value="{{ base64_encode($department->departmentid) }}">
                                <button type="button" onclick="window.closeStatusModal('confirm-status{{ $department->departmentid }}')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

    <!-- Legend -->
    <div class="mt-4 text-sm text-gray-500">
        <span class="mr-4"><i class="fas fa-edit"></i> Edit</span>
        <span><i class="fas fa-trash-alt text-red-600 hover:text-red-900"></i> Delete</span>
    </div>

    <script>
        // Make functions globally accessible for inline onclick handlers
        window.openStatusModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.setProperty('display', 'block', 'important');
                document.body.style.overflow = 'hidden';
            }
        };

        window.closeStatusModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.setProperty('display', 'none', 'important');
                document.body.style.overflow = '';
            }
        };
    </script>
</x-storeowner-app-layout>
