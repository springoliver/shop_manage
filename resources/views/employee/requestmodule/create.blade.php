@section('page_header', 'Request For Modules')
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
                        <a href="{{ route('employee.requestmodule.index') }}" class="ml-1 hover:text-gray-700">Request For Modules</a>
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

    <!-- Success/Error Messages -->
    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('employee.requestmodule.store') }}" method="POST" id="moduleForm" novalidate>
                @csrf
                
                <div class="space-y-6">
                    <!-- Module Name -->
                    <div>
                        <label for="modulename" class="block text-sm font-medium text-gray-700 mb-2">
                            Module Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               name="modulename" 
                               id="modulename" 
                               value="{{ old('modulename') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 @error('modulename') border-red-500 @enderror"
                               required
                               onblur="checkModule()">
                        <span id="existmodule" class="hidden text-sm text-red-600 mt-1">Module name already exists</span>
                        @error('modulename')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Module Description -->
                    <div>
                        <label for="moduledesc" class="block text-sm font-medium text-gray-700 mb-2">
                            Module Description <span class="text-red-500">*</span>
                        </label>
                        <textarea name="moduledesc" 
                                  id="moduledesc" 
                                  rows="10"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 @error('moduledesc') border-red-500 @enderror"
                                  required>{{ old('moduledesc') }}</textarea>
                        @error('moduledesc')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                        <button type="submit" 
                                class="px-6 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Save
                        </button>
                        <a href="{{ route('employee.requestmodule.index') }}" 
                           class="px-6 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                            Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Module name validation
        function checkModule() {
            const moduleName = document.getElementById('modulename').value;
            const existModuleSpan = document.getElementById('existmodule');
            
            if (!moduleName) {
                existModuleSpan.classList.add('hidden');
                return;
            }
            
            fetch('{{ route("employee.requestmodule.check-module-name") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    module_name: moduleName
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    existModuleSpan.classList.add('hidden');
                } else {
                    existModuleSpan.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        // Form validation
        document.getElementById('moduleForm').addEventListener('submit', function(e) {
            const moduleName = document.getElementById('modulename').value.trim();
            const moduleDesc = document.getElementById('moduledesc').value.trim();
            
            if (!moduleName) {
                e.preventDefault();
                alert('Please Enter Module name');
                return false;
            }
            
            if (!moduleDesc) {
                e.preventDefault();
                alert('Please enter module description');
                return false;
            }
            
            // Check if module name exists
            const existModuleSpan = document.getElementById('existmodule');
            if (!existModuleSpan.classList.contains('hidden')) {
                e.preventDefault();
                alert('Module name already exists');
                return false;
            }
        });
    </script>
    @endpush
</x-employee-app-layout>

