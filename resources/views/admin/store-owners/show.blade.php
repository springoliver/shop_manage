@section('page_header', 'View Store Owner')

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
                        <a href="{{ route('admin.store-owners.index') }}" class="ml-1 hover:text-gray-700 md:ml-2">Store Owner</a>
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

    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Store Owner Details</h1>
        <div class="flex space-x-3">
            <a href="{{ route('admin.store-owners.edit', $storeOwner) }}" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                Edit
            </a>
            <a href="{{ route('admin.store-owners.index') }}" class="px-4 py-2 bg-white text-gray-700 text-sm font-medium border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                Back to List
            </a>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-6">
            <!-- Profile Section -->
            <div class="flex items-start space-x-6 mb-8">
                <div class="flex-shrink-0">
                    @if($storeOwner->profile_photo)
                        <img src="{{ asset('storage/' . $storeOwner->profile_photo) }}" alt="{{ $storeOwner->firstname }}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-200">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gray-300 flex items-center justify-center border-4 border-gray-200">
                            <i class="fas fa-user text-gray-600 text-6xl"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $storeOwner->firstname }} {{ $storeOwner->lastname }}</h2>
                    <p class="text-gray-600 mb-2">@<span class="font-medium">{{ $storeOwner->username }}</span></p>
                    @if ($storeOwner->status === 'Active')
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Active
                        </span>
                    @elseif ($storeOwner->status === 'Pending Setup')
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                            Pending Setup
                        </span>
                    @elseif ($storeOwner->status === 'Suspended')
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Suspended
                        </span>
                    @else
                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            {{ $storeOwner->status }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Details Grid -->
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->emailid }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->dateofbirth->format('F d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Country</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->country ?: 'Not specified' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Address Section -->
            @if($storeOwner->address1 || $storeOwner->address2 || $storeOwner->city || $storeOwner->state || $storeOwner->zipcode)
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Address</h3>
                <dl class="grid grid-cols-1 gap-y-2">
                    @if($storeOwner->address1)
                        <div>
                            <dd class="text-sm text-gray-900">{{ $storeOwner->address1 }}</dd>
                        </div>
                    @endif
                    @if($storeOwner->address2)
                        <div>
                            <dd class="text-sm text-gray-900">{{ $storeOwner->address2 }}</dd>
                        </div>
                    @endif
                    @if($storeOwner->city || $storeOwner->state || $storeOwner->zipcode)
                        <div>
                            <dd class="text-sm text-gray-900">
                                {{ implode(', ', array_filter([$storeOwner->city, $storeOwner->state, $storeOwner->zipcode])) }}
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>
            @endif

            <!-- Account Information -->
            <div class="border-t border-gray-200 pt-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Account Information</h3>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Signup Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->signupdate->format('F d, Y h:i A') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Edit Date</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->editdate->format('F d, Y h:i A') }}</dd>
                    </div>
                    @if($storeOwner->lastlogindate)
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Last Login</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->lastlogindate->format('F d, Y h:i A') }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Terms Accepted</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $storeOwner->accept_terms }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-admin-app-layout>

