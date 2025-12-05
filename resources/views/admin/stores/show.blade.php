@section('page_header', 'View Store')

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
                        <a href="{{ route('admin.stores.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Store</a>
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

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Store Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.stores.edit', $store) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                <i class="fas fa-edit mr-2"></i>
                Edit
            </a>
            <a href="{{ route('admin.stores.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                <i class="fas fa-arrow-left mr-2"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Store Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-store mr-2"></i>
                Store Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Logo -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-2">Logo</label>
                    @if($store->logofile)
                        <img src="{{ asset('storage/' . $store->logofile) }}" alt="{{ $store->store_name }}" class="w-32 h-32 object-cover border-2 border-gray-300 rounded">
                    @else
                        <div class="w-32 h-32 bg-gray-200 border-2 border-gray-300 rounded flex items-center justify-center">
                            <span class="text-gray-400">No Logo</span>
                        </div>
                    @endif
                </div>

                <!-- Store Owner Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Store Owner Name</label>
                    <p class="text-gray-900">
                        @if($store->storeOwner)
                            {{ $store->storeOwner->firstname }} {{ $store->storeOwner->lastname }}
                        @else
                            <span class="text-gray-400">N/A</span>
                        @endif
                    </p>
                </div>

                <!-- Store Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Store Name</label>
                    <p class="text-gray-900">{{ $store->store_name }}</p>
                </div>

                <!-- Store Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Store Type</label>
                    <p class="text-gray-900">{{ $store->getStoreTypeName() }}</p>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                    <div>
                        @if ($store->status === 'Active')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                        @elseif ($store->status === 'Pending Setup')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                Pending Setup
                            </span>
                        @elseif ($store->status === 'Suspended')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Suspended
                            </span>
                        @elseif ($store->status === 'Closed')
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Closed
                            </span>
                        @else
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $store->status }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contact Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-envelope mr-2"></i>
                Contact Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Store Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Store Email</label>
                    <p class="text-gray-900">{{ $store->store_email }}</p>
                </div>

                <!-- Manager Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Manager Email</label>
                    <p class="text-gray-900">{{ $store->manager_email ?: 'N/A' }}</p>
                </div>

                <!-- Website URL -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Website URL</label>
                    @if($store->website_url)
                        <a href="{{ $store->website_url }}" target="_blank" class="text-blue-600 hover:underline">
                            {{ $store->website_url }}
                        </a>
                    @else
                        <p class="text-gray-400">N/A</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Location Information Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i>
                Location Information
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                    <p class="text-gray-900">{{ $store->full_google_address ?: 'N/A' }}</p>
                </div>

                <!-- Latitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Latitude</label>
                    <p class="text-gray-900">{{ $store->latitude ?: 'N/A' }}</p>
                </div>

                <!-- Longitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Longitude</label>
                    <p class="text-gray-900">{{ $store->longitude ?: 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Store Hours Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-clock mr-2"></i>
                Store Hours
            </h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $days = [
                        'monday' => 'Monday',
                        'tuesday' => 'Tuesday',
                        'wednesday' => 'Wednesday',
                        'thursday' => 'Thursday',
                        'friday' => 'Friday',
                        'saturday' => 'Saturday',
                        'sunday' => 'Sunday'
                    ];
                @endphp

                @foreach($days as $key => $day)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                        <span class="font-medium text-gray-700 w-32">{{ $day }}</span>
                        <div class="flex-1 text-gray-900">
                            @if($store->{$key.'_dayoff'} === 'Yes')
                                <span class="text-red-600 font-medium">Closed</span>
                            @elseif($store->{$key.'_hour_from'} && $store->{$key.'_hour_to'})
                                {{ \Carbon\Carbon::parse($store->{$key.'_hour_from'})->format('h:i A') }} -
                                {{ \Carbon\Carbon::parse($store->{$key.'_hour_to'})->format('h:i A') }}
                            @else
                                <span class="text-gray-400">Not Set</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Metadata Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                Metadata
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Created Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created Date</label>
                    <p class="text-gray-900">{{ $store->insertdate ? $store->insertdate->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>

                <!-- Created By -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Created By</label>
                    <p class="text-gray-900">{{ $store->insertby ?: 'N/A' }}</p>
                </div>

                <!-- Last Modified Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Last Modified</label>
                    <p class="text-gray-900">{{ $store->editdate ? $store->editdate->format('M d, Y h:i A') : 'N/A' }}</p>
                </div>

                <!-- Last Modified By -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Modified By</label>
                    <p class="text-gray-900">{{ $store->editby ?: 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>
</x-admin-app-layout>

