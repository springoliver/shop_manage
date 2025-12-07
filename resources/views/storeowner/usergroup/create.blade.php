@section('page_header', 'Add User Group')

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
                        <a href="{{ route('storeowner.usergroup.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">User Groups</a>
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
        <h1 class="text-2xl font-semibold text-gray-800">Add User Group</h1>
    </div>

    <!-- Info Message -->
    <div class="mb-4 text-sm text-gray-600">
        <span>All fields marked with <span class="text-red-500">*</span> are compulsory</span>
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

    <form method="POST" action="{{ route('storeowner.usergroup.store') }}" id="myform" class="space-y-6">
        @csrf

        <!-- User Group Name -->
        <div class="flex items-start gap-4">
            <label for="ugname" class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                UserGroup Name<span class="text-red-500"> *</span>
            </label>
            <div class="w-3/4">
                <x-text-input id="ugname" class="block w-full" type="text" name="ugname" :value="old('ugname')" maxlength="30" required />
                <span id="existugerror" class="error text-red-500" style="display: none;">UserGroup Name Already Exist</span>
                <x-input-error :messages="$errors->get('ugname')" class="mt-2" />
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('storeowner.usergroup.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                Cancel
            </a>
            <button type="button" onclick="checkusergroupname()" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                Save
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
        function checkusergroupname() {
            var ugname = document.getElementById('ugname').value;
            if (!ugname) {
                alert('Please enter UserGroup name');
                return false;
            }

            fetch('{{ route('storeowner.usergroup.check-name') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ ugname: ugname })
            })
            .then(response => response.text())
            .then(data => {
                var existError = document.getElementById('existugerror');
                var form = document.getElementById('myform');
                
                if (data == '1') {
                    existError.style.display = 'block';
                    document.getElementById('ugname').focus();
                    return false;
                } else {
                    existError.style.display = 'none';
                    form.submit();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Prevent form submission on Enter key
        document.addEventListener('keypress', function(e) {
            if (e.keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

