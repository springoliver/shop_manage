@section('page_header', 'Add Module')

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
                        <a href="{{ route('admin.modules.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Modules</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add Module</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Module</h1>
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
                <h3 class="text-lg font-semibold text-gray-800">Module Information</h3>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('admin.modules.store') }}" method="POST">
                @csrf

                <!-- Module Name -->
                <div class="mb-6">
                    <label for="module" class="block text-sm font-medium text-gray-700 mb-2">
                        Module <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="module" id="module" value="{{ old('module') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('module') border-red-500 @enderror"
                        required>
                    @error('module')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Module Description -->
                <div class="mb-6">
                    <label for="module_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Module Description <span class="text-red-500">*</span>
                    </label>
                    <textarea name="module_description" id="module_description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('module_description') border-red-500 @enderror"
                        required>{{ old('module_description') }}</textarea>
                    @error('module_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Module Detailed Info -->
                <div class="mb-6">
                    <label for="module_detailed_info" class="block text-sm font-medium text-gray-700 mb-2">
                        Module Detailed Info
                    </label>
                    <textarea name="module_detailed_info" id="module_detailed_info" rows="5"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('module_detailed_info') border-red-500 @enderror">{{ old('module_detailed_info') }}</textarea>
                    @error('module_detailed_info')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Free Days -->
                <div class="mb-6">
                    <label for="free_days" class="block text-sm font-medium text-gray-700 mb-2">
                        Free Days <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="free_days" id="free_days" value="{{ old('free_days', 0) }}" min="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('free_days') border-red-500 @enderror"
                        required>
                    @error('free_days')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price Fields Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <!-- Price 1 Month -->
                    <div>
                        <label for="price_1months" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (1 Month) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price_1months" id="price_1months" value="{{ old('price_1months', '0.00') }}" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('price_1months') border-red-500 @enderror"
                            required>
                        @error('price_1months')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price 3 Months -->
                    <div>
                        <label for="price_3months" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (3 Months) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price_3months" id="price_3months" value="{{ old('price_3months', '0.00') }}" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('price_3months') border-red-500 @enderror"
                            required>
                        @error('price_3months')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price 6 Months -->
                    <div>
                        <label for="price_6months" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (6 Months) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price_6months" id="price_6months" value="{{ old('price_6months', '0.00') }}" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('price_6months') border-red-500 @enderror"
                            required>
                        @error('price_6months')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price 12 Months -->
                    <div>
                        <label for="price_12months" class="block text-sm font-medium text-gray-700 mb-2">
                            Price (12 Months) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="price_12months" id="price_12months" value="{{ old('price_12months', '0.00') }}" step="0.01" min="0"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('price_12months') border-red-500 @enderror"
                            required>
                        @error('price_12months')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('status') border-red-500 @enderror"
                        required>
                        <option value="Enable" {{ old('status', 'Enable') == 'Enable' ? 'selected' : '' }}>Enable</option>
                        <option value="Disable" {{ old('status') == 'Disable' ? 'selected' : '' }}>Disable</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-start space-x-4">
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                    <a href="{{ route('admin.modules.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-app-layout>

