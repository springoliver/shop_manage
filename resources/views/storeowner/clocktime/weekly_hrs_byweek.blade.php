@section('page_header', 'Weekly Hours by Week')

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
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.clocktime.allemployee_weeklyhrs') }}" class="ml-1 hover:text-gray-700">Weekly Hours</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Week {{ $weekno }} - {{ $year }}</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow mb-6">
        <ul class="flex border-b border-gray-200" role="tablist">
            <li>
                <a href="{{ route('storeowner.clocktime.compare_weekly_hrs') }}" 
                   class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
                    Employee Hours
                </a>
            </li>
            <li>
                <a href="{{ route('storeowner.clocktime.employee_holidays') }}" 
                   class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
                    Employee Holidays
                </a>
            </li>
            <li>
                <a href="{{ route('storeowner.clocktime.allemployee_weeklyhrs') }}" 
                   class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
                    Weekly Hours
                </a>
            </li>
            <li>
                <a href="{{ route('storeowner.clocktime.monthly_hrs_allemployee') }}" 
                   class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
                    Monthly Hours
                </a>
            </li>
        </ul>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="table-new">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays Hrs Taken</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays Days Taken</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sunday Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime Hours</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime Hours 2</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sick Pay</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Holiday hrs</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Holiday notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($empPayrollHrs->count() > 0)
                        @foreach($empPayrollHrs as $payroll)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($payroll->week_end) && isset($payroll->weekno))
                                        {{ \Carbon\Carbon::parse($payroll->week_end)->format('Y-m-d') }} - {{ $payroll->weekno }}
                                    @else
                                        Week {{ $payroll->weekno ?? 'N/A' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->firstname ?? '' }} {{ $payroll->lastname ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $payroll->year }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)$payroll->hours_worked, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->holiday_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->holiday_days ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->sunday_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->owertime1_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->owertime2_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->sickpay_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->extras1_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)($payroll->extras2_hrs ?? 0), 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->notes ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="13" class="px-6 py-4 text-center text-sm text-gray-500">
                                No records found for this week.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-storeowner-app-layout>

