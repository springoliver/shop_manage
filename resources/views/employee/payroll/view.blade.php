@section('page_header')
    {{ ucfirst($myPayroll['store_name'] ?? '') }}
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
                        <a href="{{ route('employee.payroll.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">My Payroll</a>
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
        <a href="{{ route('employee.payroll.index') }}" class="text-orange-600 hover:text-orange-800">
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
                        {{ ucfirst($myPayroll['firstname'] ?? '') }} {{ ucfirst($myPayroll['lastname'] ?? '') }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Group Name:</label>
                    <div class="text-sm text-gray-900">
                        {{ $myPayroll['groupname'] ?? '' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email:</label>
                    <div class="text-sm text-gray-900">
                        {{ $myPayroll['emailid'] ?? '' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Week Number:</label>
                    <div class="text-sm text-gray-900">
                        {{ $myPayroll['weeknumber'] ?? '' }}
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payslip Name:</label>
                    <div class="text-sm text-gray-900">
                        {{ $myPayroll['payslipname'] ?? '' }}
                    </div>
                </div>

                @if($payroll->count() > 0)
                    <div class="mt-6 border-t border-gray-200 pt-6">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Day:</label>
                            <label class="block text-sm font-medium text-gray-700">Total Hours</label>
                        </div>

                        @foreach($payroll as $detail)
                            <div class="flex items-start gap-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                                <div class="w-1/4">
                                    <label class="block text-sm font-medium text-gray-700">{{ $detail->weekday }}:</label>
                                </div>
                                <div class="w-3/4">
                                    <div class="text-sm text-gray-900">{{ $detail->total_hours ?? 0 }} Hours</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-employee-app-layout>

