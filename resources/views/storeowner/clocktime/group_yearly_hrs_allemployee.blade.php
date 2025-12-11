@section('page_header', 'Employee Holidays')

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

    <!-- Search and Export -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <div class="block text-sm font-medium text-gray-700 mr-4">Search:</div>
                <input type="text" id="searchbox" placeholder="Enter Keyword" 
                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
            </div>
            @if($empPayrollHrs->count() > 0)
                <div>
                    <a href="{{ route('storeowner.clocktime.export-group-all-employee-hols', ['year' => base64_encode($year)]) }}" 
                       class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Export Summary for {{ $year }}
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="table-new">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salary Method</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours Worked</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Holidays</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extras</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays Taken</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($empPayrollHrs->count() > 0)
                        @foreach($empPayrollHrs as $payroll)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->year }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $payroll->firstname }} {{ $payroll->lastname }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ ucfirst($payroll->sallary_method) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)$payroll->hours_worked, 2) }}
                                </td>
                                
                                @if($payroll->sallary_method == 'hourly')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ floor((float)$payroll->holiday_calculated) }} hrs
                                    </td>
                                @else
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$payroll->holiday_days_counted, 2) }} days
                                    </td>
                                @endif
                                
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ floor((float)$payroll->extra_holiday_calculated) }}
                                </td>
                                
                                @if($payroll->sallary_method == 'hourly')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$payroll->holiday_hrs, 2) }} hrs
                                    </td>
                                @else
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$payroll->holiday_days, 2) }} days
                                    </td>
                                @endif
                                
                                @if($payroll->sallary_method == 'hourly')
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$payroll->holiday_calculated - (float)$payroll->holiday_hrs, 2) }} hrs
                                    </td>
                                @else
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format((float)$payroll->holiday_days_counted - (float)$payroll->holiday_days, 2) }} days
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        // Search functionality
        document.getElementById('searchbox')?.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const table = document.getElementById('table-new');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

