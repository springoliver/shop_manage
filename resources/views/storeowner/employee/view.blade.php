@section('page_header', ucfirst($employee->firstname) . ' ' . ucfirst($employee->lastname))

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
                        <a href="{{ route('storeowner.employee.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Employees</a>
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
        <h1 class="text-2xl font-semibold text-gray-800">{{ ucfirst($employee->firstname) }} {{ ucfirst($employee->lastname) }}</h1>
        <a href="{{ route('storeowner.employee.index') }}" class="text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-2"></i> Back
        </a>
    </div>

    <!-- Employee Details -->
    <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Group Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Group Name<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $group->groupname ?? 'N/A' }}</p>
            </div>

            <!-- Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Name<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ ucfirst($employee->firstname) }} {{ ucfirst($employee->lastname) }}</p>
            </div>

            <!-- Username -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->username }}</p>
            </div>

            <!-- Email Id -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email Id<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->emailid }}</p>
            </div>

            <!-- Phone -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Phone<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->phone }}</p>
            </div>

            <!-- Date of Birth -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->dateofbirth ? $employee->dateofbirth->format('m-d-Y') : 'N/A' }}</p>
            </div>

            <!-- Address 1 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address 1<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->address1 }}</p>
            </div>

            <!-- Address 2 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Address 2<span class="text-red-500"> :</span></label>
                <p class="text-gray-900">{{ $employee->address2 }}</p>
            </div>

            <!-- Profile Photo -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Profile Photo<span class="text-red-500"> :</span></label>
                @if($employee->profile_photo)
                    <img src="{{ Storage::url($employee->profile_photo) }}" alt="Profile Photo" class="h-48 w-48 object-cover rounded border border-gray-300">
                @else
                    <p class="text-gray-500">No photo uploaded</p>
                @endif
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

