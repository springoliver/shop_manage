@section('page_header', 'Clock-In-Out Settings')

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
                        <a href="{{ route('storeowner.clocktime.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Clock-in-out</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Settings</span>
                    </div>
                </li>
            </ol>
        </nav>
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

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-6">Clock-In-Out Settings</h2>
        
        <form action="{{ route('storeowner.clocktime.update-settings') }}" method="POST">
            @csrf
            
            <!-- Break Events Setting -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Enable Break Start/End Events
                </label>
                <p class="text-sm text-gray-500 mb-4">
                    When enabled, employees can start and end breaks during their shifts. When disabled, break buttons will be hidden in the mobile app.
                </p>
                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="radio" name="enable_break_events" value="Yes" 
                               {{ old('enable_break_events', $store->enable_break_events ?? 'Yes') == 'Yes' ? 'checked' : '' }}
                               class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Enabled</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="enable_break_events" value="No" 
                               {{ old('enable_break_events', $store->enable_break_events ?? 'Yes') == 'No' ? 'checked' : '' }}
                               class="form-radio h-4 w-4 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-gray-700">Disabled</span>
                    </label>
                </div>
                @error('enable_break_events')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('storeowner.clocktime.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</x-storeowner-app-layout>

