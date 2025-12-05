@section('page_header', 'General Settings')

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
                        <a href="{{ route('admin.settings.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">General Settings</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">General Settings</h1>
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
                <h3 class="text-lg font-semibold text-gray-800">General Settings</h3>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('admin.settings.update', $setting) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Setting Field (Title as Label) -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ $setting->title }}
                    </label>
                    <input type="text" name="value" id="value" value="{{ old('value', $setting->value) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('value') border-red-500 @enderror"
                        required>
                    @error('value')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Hidden field for title to maintain it during update -->
                <input type="hidden" name="title" value="{{ $setting->title }}">

                <!-- Form Actions -->
                <div class="flex items-center justify-start space-x-4">
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                    <a href="{{ route('admin.settings.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-app-layout>

