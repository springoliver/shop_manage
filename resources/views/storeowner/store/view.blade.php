@section('page_header', $store->store_name)

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
                        <a href="{{ route('storeowner.store.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">My Store</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">View</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Back Button -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $store->store_name }}</h1>
        <a href="{{ route('storeowner.store.index') }}" class="text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-2"></i> Back
        </a>
    </div>

    <!-- Store Details -->
    <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Store Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Store Type<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $store->storeType->store_type ?? 'N/A' }}</p>
            </div>

            <!-- Website URL -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Website URL<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">
                    <a href="{{ $store->website_url }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                        {{ $store->website_url }}
                    </a>
                </p>
            </div>

            <!-- Address -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $store->full_google_address }}</p>
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $store->store_email }}</p>
            </div>

            <!-- Manager Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Manager Email<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $store->manager_email }}</p>
            </div>

            <!-- Logo File -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Logo File<span class="text-red-500"> :</span></label>
                @if($store->logofile)
                    <img src="{{ asset('storage/' . $store->logofile) }}" alt="Store Logo" class="h-32 w-32 object-cover rounded border border-gray-300">
                @else
                    <p class="text-gray-500">No logo uploaded</p>
                @endif
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

