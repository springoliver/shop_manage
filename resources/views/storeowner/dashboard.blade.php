@section('page_header', 'Dashboard')

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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Dashboard</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
    </div>

    <!-- Welcome Message -->
    <div class="mb-6">
        <p class="text-gray-600">
            Welcome, <span class="font-semibold text-gray-800">{{ Auth::guard('storeowner')->user()->firstname }} {{ Auth::guard('storeowner')->user()->lastname }}</span>!
        </p>
        <p class="text-sm text-gray-500 mt-1">
            Username: {{ Auth::guard('storeowner')->user()->username }} | Email: {{ Auth::guard('storeowner')->user()->emailid }}
        </p>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('storeowner.store.index') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">My Stores</a>
            <a href="{{ route('storeowner.usergroup.index') }}" class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">User Groups</a>
            <a href="{{ route('storeowner.department.index') }}" class="bg-purple-600 text-white px-4 py-2 rounded text-sm hover:bg-purple-700">Departments</a>
            <a href="{{ route('storeowner.profile.edit') }}" class="bg-gray-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-700">Edit Profile</a>
        </div>
    </div>
</x-storeowner-app-layout>

