@section('page_header', 'Yearly Employee Holiday Hours by Year')

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
                        <a href="{{ route('storeowner.clocktime.employee_holidays') }}" class="ml-1 hover:text-gray-700">Employee Holidays</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($employee->employeeid)]) }}" class="ml-1 hover:text-gray-700">Yearly Hours</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Year {{ $year }}</span>
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
                   class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
                    Employee Holidays
                </a>
            </li>
            <li>
                <a href="{{ route('storeowner.clocktime.allemployee_weeklyhrs') }}" 
                   class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
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

    @if($empPayrollHrsYearly->count() > 0 && $employee)
        @php
            $salaryMethod = $empPayrollHrsYearly->first()->sallary_method ?? 'hourly';
        @endphp
        
        <!-- Note -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center">
                <p class="text-sm text-gray-700">
                    <strong>Note:</strong> {{ $employee->firstname }} {{ $employee->lastname }}'s Hours calculated based on {{ ucfirst($salaryMethod) }} pay method.
                </p>
                @if($salaryMethod == 'hourly' && isset($empPayrollHrsYearly->first()->fourmonthavg_hrs))
                    <p class="text-sm text-gray-700">
                        <strong>1/5 th of weekly hrs (Last 13 weeks):</strong> {{ number_format((float)$empPayrollHrsYearly->first()->fourmonthavg_hrs, 2) }} hrs.
                    </p>
                @endif
            </div>
        </div>

        @if($salaryMethod == 'yearly')
            <!-- Yearly Salary Method Summary Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays Days Entitlement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Taken</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Remaining</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($empPayrollHrsYearly as $payroll)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.yearly-hrs-by-year-employee', ['employeeid' => base64_encode($payroll->employeeid), 'year' => base64_encode($payroll->year)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $payroll->year }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll->firstname }} {{ $payroll->lastname }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.weekly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ number_format((float)$payroll->holiday_days_counted, 2) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->holiday_days, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->holiday_days_counted - (float)$payroll->holiday_days, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <!-- Hourly Salary Method Summary Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Worked</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday Hrs Entitlement</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extras</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday hrs Taken</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holiday hrs Remaining</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($empPayrollHrsYearly as $payroll)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.yearly-hrs-by-year-employee', ['employeeid' => base64_encode($payroll->employeeid), 'year' => base64_encode($payroll->year)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $payroll->year }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll->firstname }} {{ $payroll->lastname }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.weekly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ number_format((float)$payroll->hours_worked, 2) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ floor((float)$payroll->holiday_calculated) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->extra_holiday_calculated, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->holiday_hrs, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->holiday_calculated - (float)$payroll->holiday_hrs, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <!-- Weekly Breakdown Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-new">
                        <thead>
                            <tr class="bg-gray-50">
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
                                                {{ $payroll->weekno ?? 'N/A' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $payroll->firstname }} {{ $payroll->lastname }}
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
                                            {{ $payroll->notes ?? '' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="12" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No weekly records found for this year.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-center text-gray-500">No records found.</p>
        </div>
    @endif
</x-storeowner-app-layout>

