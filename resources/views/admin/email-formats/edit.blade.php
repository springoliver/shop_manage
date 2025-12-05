@section('page_header', 'Email Formats')

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
                        <a href="{{ route('admin.email-formats.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Email Formats</a>
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
        <h1 class="text-2xl font-semibold text-gray-800">Email Formats</h1>
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
                <h3 class="text-lg font-semibold text-gray-800">Email Formats</h3>
            </div>
        </div>

        <!-- Card Body -->
        <div class="p-6">
            <form action="{{ route('admin.email-formats.update', $emailFormat) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Title Field -->
                <div class="mb-6">
                    <label for="vartitle" class="block text-sm font-medium text-gray-700 mb-2">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="vartitle" id="vartitle" value="{{ old('vartitle', $emailFormat->vartitle) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('vartitle') border-red-500 @enderror"
                        required>
                    @error('vartitle')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Subject Field -->
                <div class="mb-6">
                    <label for="varsubject" class="block text-sm font-medium text-gray-700 mb-2">
                        Subject <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="varsubject" id="varsubject" value="{{ old('varsubject', $emailFormat->varsubject) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 @error('varsubject') border-red-500 @enderror"
                        required>
                    @error('varsubject')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Variables Field (Readonly) -->
                <div class="mb-6">
                    <label for="variables" class="block text-sm font-medium text-gray-700 mb-2">
                        Variables
                    </label>
                    <textarea name="variables" id="variables" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-50 focus:ring-gray-500 focus:border-gray-500"
                        readonly>{{ $emailFormat->variables }}</textarea>
                </div>

                <!-- Email Format Field -->
                <div class="mb-6">
                    <label for="varmailformat" class="block text-sm font-medium text-gray-700 mb-2">
                        Email Format <span class="text-red-500">*</span>
                    </label>
                    <textarea name="varmailformat" id="varmailformat" rows="10"
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500 font-mono text-sm @error('varmailformat') border-red-500 @enderror"
                        required>{{ old('varmailformat', $emailFormat->varmailformat) }}</textarea>
                    @error('varmailformat')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-start space-x-4">
                    <button type="submit" class="px-6 py-2 bg-gray-800 text-white font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Save
                    </button>
                    <a href="{{ route('admin.email-formats.index') }}" class="px-6 py-2 bg-white text-gray-700 font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-admin-app-layout>

