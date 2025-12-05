@section('page_header', 'Edit User Group')

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
                        <a href="{{ route('admin.user-groups.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">User Groups</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit User Group</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit User Group</h1>
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
                <h3 class="text-lg font-semibold text-gray-800">Edit User Group</h3>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('admin.user-groups.update', $userGroup) }}" method="POST" id="userGroupForm">
                @csrf
                @method('PUT')

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
                            <option value="{{ $store->storeid }}" {{ old('storeid', $userGroup->storeid) == $store->storeid ? 'selected' : '' }}>
                                {{ $store->store_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('storeid')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- User Group Name -->
                <div class="mb-6">
                    <label for="groupname" class="block text-sm font-medium text-gray-700 mb-2">
                        User Group Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="groupname" id="groupname" value="{{ old('groupname', $userGroup->groupname) }}" maxlength="255"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('groupname') border-red-500 @enderror"
                        required onblur="checkGroupName(this.value)">
                    <span id="existgroupnameerror" class="text-red-600 text-sm mt-1" style="display:none">Group Name Already Exist</span>
                    @error('groupname')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Access Permission -->
                <div class="mb-6">
                    <label for="level_access" class="block text-sm font-medium text-gray-700 mb-2">
                        Access Permission <span class="text-red-500">*</span>
                    </label>
                    <select name="level_access" id="level_access"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('level_access') border-red-500 @enderror"
                        required>
                        <option value="Admin" {{ old('level_access', $userGroup->level_access) == 'Admin' ? 'selected' : '' }}>Admin</option>
                        <option value="View" {{ old('level_access', $userGroup->level_access) == 'View' ? 'selected' : '' }}>View</option>
                    </select>
                    @error('level_access')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-start space-x-4">
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                    <a href="{{ route('admin.user-groups.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function checkGroupName(groupname) {
            if (!groupname) return;
            
            fetch('{{ route("admin.user-groups.check-groupname") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    groupname: groupname,
                    usergroupid: {{ $userGroup->usergroupid }}
                })
            })
            .then(response => response.json())
            .then(data => {
                const errorSpan = document.getElementById('existgroupnameerror');
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

