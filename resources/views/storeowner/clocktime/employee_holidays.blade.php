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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Holidays</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="bg-white rounded-lg shadow mb-6">
                <ul class="nav nav-tabs flex border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('storeowner.clocktime.compare_weekly_hrs') }}" 
                           class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
                            Employee Hours
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('storeowner.clocktime.employee_holidays') }}" 
                           class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
                            Employee Holidays
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('storeowner.clocktime.allemployee_weeklyhrs') }}" 
                           class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
                            Weekly Hours
                        </a>
                    </li>
                    <li class="nav-item">
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
                    <div class="flex justify-between items-center">
                        <div class="block text-sm font-medium text-gray-700 mr-4">Search:</div>
                        <input type="text" id="searchbox" placeholder="Enter Keyword" 
                               class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <a href="#" 
                           onclick="alert('Export functionality will be implemented'); return false;"
                           class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Export holiday summary
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-new">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Year
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Employee Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Salary Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Hours Worked
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Due Holidays
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Extras
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Holidays Taken
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Holidays Remaining
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($empPayrollHrs->count() > 0)
                                @foreach($empPayrollHrs as $payroll)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">
                                                {{ $payroll->year }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="#" class="text-blue-600 hover:text-blue-800">
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
                                            {{ number_format((float)$payroll->extra_holiday_calculated, 2) }}
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

