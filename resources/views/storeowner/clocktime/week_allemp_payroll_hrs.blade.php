@section('page_header', 'Weekly total employee hours')

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
                        <a href="{{ route('storeowner.clocktime.index') }}" class="ml-1 hover:text-gray-700">Clock-in-out</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Weekly total employee hours</span>
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

    <!-- Header Info -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800">
                &lt;&lt; Back
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
            <div>
                <span class="font-semibold">Start Date:</span> 
                <span>{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}</span>
            </div>
            <div>
                <span class="font-semibold">End Date:</span> 
                <span>{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}</span>
            </div>
            @if(count($clockDetails) > 0)
                <div>
                    <span class="font-semibold">Week:</span> 
                    <span>{{ $weekDisplay }}</span>
                </div>
            @endif
        </div>
        
        @if(count($clockDetails) > 0)
            <div class="mt-4">
                <a href="{{ route('storeowner.clocktime.export-payroll-hrs', [
                    'weekid' => $calculatedWeekid,
                    'date' => \Carbon\Carbon::parse($startDate)->format('Y-m-d')
                ]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                    Export payroll hours
                </a>
            </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('storeowner.clocktime.upload-all-weekly-hours') }}" method="POST">
                @csrf
                <input type="hidden" name="weekid" value="{{ $weekDisplay }}">
                <input type="hidden" name="weekno" value="{{ $weekNumber }}">
                <input type="hidden" name="week_start" value="{{ $startDate }}">
                <input type="hidden" name="week_end" value="{{ $endDate }}">
                <input type="hidden" name="year" value="{{ $year }}">
                
                <div class="mb-4 flex items-center">
                    <i class="fas fa-clock mr-2"></i>
                    <h5 class="text-lg font-semibold">Clock-In-Out</h5>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number of days Worked</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total breaks for the week (Deducted)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Payroll Hours for the week (Numeric)</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($clockDetails ?? [] as $detail)
                                <input type="hidden" name="employeeid[]" value="{{ $detail['employeeid'] }}">
                                <input type="hidden" name="storeid[]" value="{{ $detail['storeid'] }}">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $weekDisplay }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <a href="{{ route('storeowner.clocktime.week-clock-time', [
                                            'employeeid' => base64_encode($detail['employeeid']),
                                            'date' => \Carbon\Carbon::parse($startDate)->format('Y-m-d')
                                        ]) }}" 
                                           class="text-blue-600 hover:text-blue-800" 
                                           title="Click here to manage clock in-out of {{ ucfirst($detail['firstname']) }} {{ ucfirst($detail['lastname']) }}">
                                            {{ ucfirst($detail['firstname']) }} {{ ucfirst($detail['lastname']) }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $detail['numOfdaysWorded'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @php
                                            $breakHours = floor($detail['totalBreakout'] / 60);
                                            $breakMinutes = $detail['totalBreakout'] % 60;
                                        @endphp
                                        {{ $breakHours }} hr {{ $breakMinutes }} min
                                        <input type="hidden" name="deducted_time[]" value="{{ $breakHours }}:{{ $breakMinutes }}">
                                        <input type="hidden" name="deducted_hour[]" value="{{ $detail['totalBreakout'] }}">
                                        <input type="hidden" name="hours_worked[]" value="{{ number_format($detail['total'], 2) }}">
                                        <input type="hidden" name="numberofdaysworked[]" value="{{ $detail['numOfdaysWorded'] }}">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($detail['status'] != 'clockout')
                                            {{ number_format($detail['total'], 2) }}
                                        @else
                                            Still Working...
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">No payroll data found</td>
                                </tr>
                            @endforelse
                            @if(count($clockDetails) > 0)
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-right font-semibold text-sm">
                                        Total Payroll Hour (Numeric):
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                                        {{ number_format($totalPayrol, 2) }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                
                @if(count($clockDetails) > 0)
                    <div class="mt-6 flex justify-between items-center">
                        <div>
                            <a href="{{ route('storeowner.clocktime.generate-week-payslip') }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                Generate payslips for week {{ $weekDisplay }}
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                Upload payroll hours for week: {{ $weekDisplay }}
                            </button>
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>
</x-storeowner-app-layout>

