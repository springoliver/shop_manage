@section('page_header', ucfirst($employee->firstname) . ' ' . ucfirst($employee->lastname))
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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">View</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Edit Profile Button -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('employee.profile.edit') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <i class="fas fa-edit mr-2"></i>Edit Profile
        </a>
    </div>

    <!-- Profile Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form class="form-horizontal">
                <!-- Group Name -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Group Name <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $group->groupname ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Name -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Name <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ ucfirst($employee->firstname) }} {{ ucfirst($employee->lastname) }}</span>
                    </div>
                </div>

                <!-- Username -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Username <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $employee->username }}</span>
                    </div>
                </div>

                <!-- Email Id -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Email Id <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $employee->emailid }}</span>
                    </div>
                </div>

                <!-- Profile Photo -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Profile Photo <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        @if($employee->profile_photo && Storage::disk('public')->exists($employee->profile_photo))
                            <img src="{{ Storage::url($employee->profile_photo) }}" alt="Profile Photo" class="h-48 w-48 object-cover rounded border border-gray-300">
                        @else
                            <div class="h-48 w-48 bg-gray-200 rounded border border-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-6xl"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Phone -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Phone <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $employee->phone ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Address 1 -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Address 1 <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $employee->address1 ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Address 2 -->
                <div class="flex items-start gap-4 mb-6 pb-4 border-b border-gray-200">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Address 2 <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">{{ $employee->address2 ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="flex items-start gap-4 mb-6">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        Date of Birth <span class="text-gray-400">:</span>
                    </label>
                    <div class="w-3/4">
                        <span class="text-sm text-gray-900">
                            @if($employee->dateofbirth)
                                {{ \Carbon\Carbon::parse($employee->dateofbirth)->format('d-m-Y') }}
                            @else
                                N/A
                            @endif
                        </span>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-employee-app-layout>

