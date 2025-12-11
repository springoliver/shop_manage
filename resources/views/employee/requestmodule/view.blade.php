@section('page_header', 'View Module Request')
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
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('employee.requestmodule.index') }}" class="ml-1 hover:text-gray-700">Request For Modules</a>
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

    <!-- Back Link -->
    <div class="mb-4 flex justify-end">
        <a href="{{ route('employee.requestmodule.index') }}" class="inline-flex items-center text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-2"></i>
            Back
        </a>
    </div>

    <!-- View Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="space-y-6">
                <!-- Module Name -->
                <div class="flex items-start">
                    <label class="w-1/4 text-sm font-medium text-gray-700">Module name:</label>
                    <div class="w-3/4 text-sm text-gray-900">
                        {{ $module->module_name }}
                    </div>
                </div>

                <!-- Module Description -->
                <div class="flex items-start">
                    <label class="w-1/4 text-sm font-medium text-gray-700">Module Description:</label>
                    <div class="w-3/4 text-sm text-gray-900">
                        {!! nl2br(e($module->module_description)) !!}
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-start">
                    <label class="w-1/4 text-sm font-medium text-gray-700">Status:</label>
                    <div class="w-3/4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $module->status === 'Seen' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                            {{ $module->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-employee-app-layout>

