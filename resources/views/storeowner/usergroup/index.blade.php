@section('page_header', 'User Groups')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">User Groups</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">User Groups</h1>
        <a href="{{ route('storeowner.usergroup.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
            <i class="fas fa-plus mr-2"></i>
            Add
        </a>
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

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User Group Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($userGroups as $userGroup)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $userGroup->groupname }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <!-- Edit -->
                                        <a href="{{ route('storeowner.usergroup.edit', $userGroup->usergroupid) }}" class="text-gray-600 hover:text-gray-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Delete (only for store-specific groups, not global) -->
                                        @if($userGroup->usergroup_storeid != '0')
                                            <form action="{{ route('storeowner.usergroup.destroy', $userGroup->usergroupid) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this user group?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <!-- View -->
                                        <button type="button" onclick="getModuleView({{ $userGroup->usergroupid }})" class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No user groups found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 text-sm text-gray-600">
        <span class="font-medium">Legend(s):</span>
        <span class="ml-4"><i class="fas fa-edit text-gray-600"></i> Edit</span>
        <span class="ml-4"><i class="fas fa-trash-alt text-red-600"></i> Delete</span>
        <span class="ml-4"><i class="fas fa-eye text-blue-600"></i> View</span>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="confirm-delete" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Delete</h3>
                    <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mb-4">
                    <p class="text-sm text-gray-700">Are you sure you want to delete this user group?</p>
                </div>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <a id="delete-link" href="#" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Delete
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Module View Modal Container -->
    <div id="modalcontent"></div>

    @push('scripts')
    <script>
        function getModuleView(usergroupid) {
            fetch('{{ route('storeowner.usergroup.view') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ usergroupid: usergroupid })
            })
            .then(response => response.text())
            .then(data => {
                var modalContent = document.getElementById('modalcontent');
                modalContent.innerHTML = data;
                
                // Set up close handlers after modal is loaded
                setTimeout(function() {
                    var modal = document.getElementById('edit_roster');
                    if (modal) {
                        // Close on outside click
                        modal.addEventListener('click', function(e) {
                            if (e.target === this) {
                                closeModal();
                            }
                        });
                    }
                }, 100);
            });
        }

        function closeModal() {
            var modal = document.getElementById('edit_roster');
            if (modal) {
                modal.remove();
            }
            // Also clear the modalcontent container
            var modalContent = document.getElementById('modalcontent');
            if (modalContent) {
                modalContent.innerHTML = '';
            }
        }

        // Delete modal handlers
        document.addEventListener('DOMContentLoaded', function() {
            var deleteModal = document.getElementById('confirm-delete');
            var deleteLink = document.getElementById('delete-link');
            
            // This will be handled by the form's onsubmit, but we can also add click handlers if needed
        });

        function closeDeleteModal() {
            document.getElementById('confirm-delete').classList.add('hidden');
        }
    </script>
    @endpush
</x-storeowner-app-layout>

