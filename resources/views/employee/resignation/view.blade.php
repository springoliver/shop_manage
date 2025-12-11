@section('page_header')
    {{ ucfirst($resignation['firstname'] ?? '') }} {{ ucfirst($resignation['lastname'] ?? '') }}
@endsection
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
                        <a href="{{ route('employee.resignation.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Resignation</a>
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
    <div class="mb-6 flex justify-end">
        <a href="{{ route('employee.resignation.index') }}" class="text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-1"></i> Back
        </a>
    </div>

    <!-- View Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employee Name:</label>
                    <div class="text-sm text-gray-900">
                        {{ ucfirst($resignation['firstname'] ?? '') }} {{ ucfirst($resignation['lastname'] ?? '') }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subject:</label>
                    <div class="text-sm text-gray-900">
                        {{ $resignation['subject'] ?? '' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description:</label>
                    <div class="text-sm text-gray-900 whitespace-pre-wrap">
                        {{ $resignation['description'] ?? '' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Insert Date Time:</label>
                    <div class="text-sm text-gray-900">
                        {{ \Carbon\Carbon::parse($resignation['insertdatetime'] ?? now())->format('d-m-Y H:i:s') }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Insert IP:</label>
                    <div class="text-sm text-gray-900">
                        {{ $resignation['insertip'] ?? '' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-employee-app-layout>

