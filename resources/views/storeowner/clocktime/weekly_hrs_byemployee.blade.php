@section('page_header', 'Weekly Employee Hours')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Weekly Hours - {{ $employee->firstname ?? '' }} {{ $employee->lastname ?? '' }}</span>
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

    @if($empPayrollHrs->count() > 0 && $employee)
        <!-- Search -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <div class="block text-sm font-medium text-gray-700 mr-4">Search:</div>
                    <input type="text" id="searchbox" placeholder="Enter Keyword" 
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="table-new">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Days Worked</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays (Hrs)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Holidays (Days)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sunday Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime Hours 2</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sick Pay</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bank Holiday hrs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Extra Hours</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($empPayrollHrs as $payroll)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $payroll->year }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->weekno ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->week_start ? \Carbon\Carbon::parse($payroll->week_start)->format('Y-m-d') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->firstname ?? '' }} {{ $payroll->lastname ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format((float)$payroll->hours_worked, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payroll->numberofdaysworked ?? 'N/A' }}
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
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-center text-gray-500">No records found.</p>
        </div>
    @endif

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

