@php
use Illuminate\Support\Facades\Route;
@endphp

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
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <!-- Employees -->
                <div class="bg-blue-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-user text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.employee.index') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Employees</div>
                            <div class="text-3xl font-bold">{{ $empActiveCount }}</div>
                            <div class="text-xs mt-1 opacity-90">Deactivated {{ $empDeactiveCount }}</div>
                        </a>
                    </div>
                </div>

                <!-- Employee Reviews -->
                @if(in_array('Employee Reviews', $installedModuleNames))
                <div class="bg-green-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-file-text text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.employeereviews.due-reviews') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Due Reviews</div>
                            <div class="text-3xl font-bold">{{ $employeeReviewsDueCount }}</div>
                            <div class="text-xs mt-1 opacity-90">Total {{ $employeeReviews }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Time Off Request -->
                @if(in_array('Time Off Request', $installedModuleNames))
                <div class="bg-purple-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-calendar text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.holidayrequest.index') }}?type=pending" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Pending Requests</div>
                            <div class="text-3xl font-bold">{{ $holidayRequestPendingCount }}</div>
                            <div class="text-xs mt-1 opacity-90">All {{ $holidayRequestCount }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Resignation -->
                @if(in_array('Resignation', $installedModuleNames))
                <div class="bg-orange-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-user-times text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.resignation.index') }}?type=pending" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Pending Resignation</div>
                            <div class="text-3xl font-bold">{{ $resignationPendingCount }}</div>
                            <div class="text-xs mt-1 opacity-90">All {{ $resignationCount }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Clocked-in -->
                @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.index'))
                <div class="bg-teal-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-clock text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.clocktime.index') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Clocked-in</div>
                            <div class="text-3xl font-bold">{{ $clockInCount }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Monthly Sales -->
                @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.index'))
                <div class="bg-indigo-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-chart-line text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.dailyreport.index') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">This Month</div>
                            <div class="text-2xl font-bold">€{{ number_format($dailyReport['total_sell'] ?? 0, 2) }}</div>
                            <div class="text-xs mt-1 opacity-90">Safe €{{ number_format($dailyReport['s_safe'] ?? 0, 2) }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Yearly Sales -->
                @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.index'))
                <div class="bg-pink-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-chart-bar text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.dailyreport.index') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">This Year</div>
                            <div class="text-2xl font-bold">€{{ number_format($dailyYearlyReport['total_yearly_sell'] ?? 0, 2) }}</div>
                            <div class="text-xs mt-1 opacity-90">Safe €{{ number_format($dailyYearlyReport['s_yearly_safe'] ?? 0, 2) }}</div>
                        </a>
                    </div>
                </div>
                @endif

                <!-- Delivery Dockets -->
                @if(in_array('Ordering', $installedModuleNames) && Route::has('storeowner.ordering.missing_delivery_dockets'))
                <div class="bg-red-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-file-o text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.ordering.missing_delivery_dockets') }}" class="block">
                            <div class="text-sm font-semibold uppercase mb-1">Awaiting Dockets</div>
                            <div class="text-3xl font-bold">{{ $deliveryDocketsCount }}</div>
                        </a>
                    </div>
                </div>
                @endif
            </div>

            <!-- Weekly Sales Analysis (Only if Daily Report module installed) -->
            @if(in_array('Daily Report', $installedModuleNames))
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-800">Weekly Sales Analysis</h3>
                    <form id="weekNumberForm" action="{{ route('storeowner.dashboard') }}" method="POST" class="flex items-center space-x-2">
                        @csrf
                        <button type="submit" name="week_last" class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700">
                            &lt;&lt;
                        </button>
                        <label class="text-sm font-medium text-gray-700">Week Number: <span class="font-bold">{{ $week }}</span></label>
                        <button type="submit" 
                                name="week_next" 
                                class="px-3 py-1 bg-gray-600 text-white rounded hover:bg-gray-700"
                                {{ date('W') == $week ? 'disabled' : '' }}>
                            &gt;&gt;
                        </button>
                        <input type="hidden" name="week" value="{{ $week }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                    </form>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Start Date:</span>
                        <span class="ml-2">{{ date('d-m-Y', strtotime($startDate)) }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">End Date:</span>
                        <span class="ml-2">{{ date('d-m-Y', strtotime($weekEndDateForDisplay)) }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Total Sales:</span>
                        <span class="ml-2 font-bold text-green-600">€{{ number_format($totalSales, 2) }}</span>
                    </div>
                </div>

                <!-- Weekly Comparison Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metric</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mon</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tue</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Wed</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thu</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fri</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sat</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sun</th>
                                <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Total Sales -->
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Total Sales</td>
                                @for($i = 0; $i < 7; $i++)
                                    <td class="px-3 py-3 text-sm text-center text-gray-900">
                                        €{{ number_format($currentWeekAmount1[$i][0] ?? 0, 2) }}
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-sm text-center font-bold text-green-600">€{{ number_format($totalSales, 2) }}</td>
                            </tr>

                            <!-- Compare to Last Year -->
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Compare to Last Year</td>
                                @for($i = 0; $i < 7; $i++)
                                    <td class="px-3 py-3 text-sm text-center">
                                        <span class="{{ isset($lastYearAvgData[$i]) && $lastYearAvgData[$i]['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $lastYearAvgData[$i]['percentage'] ?? '0%' }}
                                        </span>
                                        @if(isset($lastYearAvgData[$i]) && $lastYearAvgData[$i]['status'] == 'profit')
                                            <i class="fas fa-arrow-up text-green-600"></i>
                                        @elseif(isset($lastYearAvgData[$i]) && $lastYearAvgData[$i]['status'] == 'loss')
                                            <i class="fas fa-arrow-down text-red-600"></i>
                                        @endif
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-sm text-center">
                                    <span class="{{ $percentageOfTotalYearData['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $percentageOfTotalYearData['percentage'] ?? '0%' }}
                                    </span>
                                    @if($percentageOfTotalYearData['status'] == 'profit')
                                        <i class="fas fa-arrow-up text-green-600"></i>
                                    @elseif($percentageOfTotalYearData['status'] == 'loss')
                                        <i class="fas fa-arrow-down text-red-600"></i>
                                    @endif
                                </td>
                            </tr>

                            <!-- Compare to Last Week -->
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Compare to Last Week</td>
                                @for($i = 0; $i < 7; $i++)
                                    <td class="px-3 py-3 text-sm text-center">
                                        <span class="{{ isset($lastWeekAvgData[$i]) && $lastWeekAvgData[$i]['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $lastWeekAvgData[$i]['percentage'] ?? '0%' }}
                                        </span>
                                        @if(isset($lastWeekAvgData[$i]) && $lastWeekAvgData[$i]['status'] == 'profit')
                                            <i class="fas fa-arrow-up text-green-600"></i>
                                        @elseif(isset($lastWeekAvgData[$i]) && $lastWeekAvgData[$i]['status'] == 'loss')
                                            <i class="fas fa-arrow-down text-red-600"></i>
                                        @endif
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-sm text-center">
                                    <span class="{{ $percentageOfLastWeekData['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $percentageOfLastWeekData['percentage'] ?? '0%' }}
                                    </span>
                                    @if($percentageOfLastWeekData['status'] == 'profit')
                                        <i class="fas fa-arrow-up text-green-600"></i>
                                    @elseif($percentageOfLastWeekData['status'] == 'loss')
                                        <i class="fas fa-arrow-down text-red-600"></i>
                                    @endif
                                </td>
                            </tr>

                            <!-- Compare to Target -->
                            @if(!empty($targetWeekData))
                            <tr>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">Compare to Target</td>
                                @for($i = 0; $i < 7; $i++)
                                    <td class="px-3 py-3 text-sm text-center">
                                        @if(isset($targetWeekData[$i]))
                                            <span class="{{ $targetWeekData[$i]['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $targetWeekData[$i]['percentage'] }}
                                            </span>
                                            @if($targetWeekData[$i]['status'] == 'profit')
                                                <i class="fas fa-arrow-up text-green-600"></i>
                                            @elseif($targetWeekData[$i]['status'] == 'loss')
                                                <i class="fas fa-arrow-down text-red-600"></i>
                                            @endif
                                        @else
                                            <span class="text-gray-500">0%</span>
                                        @endif
                                    </td>
                                @endfor
                                <td class="px-3 py-3 text-sm text-center">
                                    @if(isset($targetWeekData[7]))
                                        <span class="{{ $targetWeekData[7]['status'] == 'profit' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $targetWeekData[7]['percentage'] }}
                                        </span>
                                        @if($targetWeekData[7]['status'] == 'profit')
                                            <i class="fas fa-arrow-up text-green-600"></i>
                                        @elseif($targetWeekData[7]['status'] == 'loss')
                                            <i class="fas fa-arrow-down text-red-600"></i>
                                        @endif
                                    @else
                                        <span class="text-gray-500">0%</span>
                                    @endif
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                <!-- Dashboard Settings -->
                <div class="mt-4 p-4 bg-gray-50 rounded">
                    <p class="text-sm font-medium text-gray-700 mb-2">Current labour comparison is based on:</p>
                    <div class="flex space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="sale_per_labour_hour" value="1" class="form-radio">
                            <span class="ml-2 text-sm text-gray-700">Daily Target</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="sale_per_labour_hour" value="2" class="form-radio">
                            <span class="ml-2 text-sm text-gray-700">Weekly Target</span>
                        </label>
                    </div>
                </div>
            </div>
            @endif

            <!-- Department Labour Analysis (Only if Employee Payroll module installed) -->
            @if(in_array('Employee Payroll', $installedModuleNames) && !empty($departments))
                @foreach($departments as $depID => $depVL)
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Labour ({{ $depVL['department'] ?? 'N/A' }})</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Metric</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Mon</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tue</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Wed</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thu</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fri</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sat</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sun</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Total Hours -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Total Hours</td>
                                    @for($i = 0; $i < 7; $i++)
                                        <td class="px-3 py-3 text-sm text-center text-gray-900">
                                            {{ number_format($currentWeekHour[$depID][$i]['tHrsFloat'] ?? 0, 2) }} Hr
                                        </td>
                                    @endfor
                                    <td class="px-3 py-3 text-sm text-center font-bold text-blue-600">
                                        {{ number_format($currentWeekHour[$depID][7]['tHrsFloat'] ?? 0, 2) }} Hr
                                    </td>
                                </tr>

                                <!-- Compare to Target -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Compare to Target</td>
                                    @for($i = 0; $i < 7; $i++)
                                        <td class="px-3 py-3 text-sm text-center">
                                            @if(isset($compareToTarget[$depID][$i]))
                                                @php
                                                    $diff = $compareToTarget[$depID][$i];
                                                @endphp
                                                <span class="{{ $diff > 0 ? 'text-red-600' : ($diff < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                                    {{ $diff > 0 ? '+' : '' }}{{ $diff }}
                                                </span>
                                                @if($diff > 0)
                                                    <i class="fas fa-arrow-up text-red-600"></i>
                                                @elseif($diff < 0)
                                                    <i class="fas fa-arrow-down text-green-600"></i>
                                                @endif
                                            @else
                                                <span class="text-gray-500">0</span>
                                            @endif
                                        </td>
                                    @endfor
                                    <td class="px-3 py-3 text-sm text-center">
                                        @if(isset($compareToTarget[$depID][7]))
                                            @php
                                                $totalDiff = $compareToTarget[$depID][7];
                                            @endphp
                                            <span class="{{ $totalDiff > 0 ? 'text-red-600' : ($totalDiff < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                                {{ $totalDiff > 0 ? '+' : '' }}{{ $totalDiff }}
                                            </span>
                                            @if($totalDiff > 0)
                                                <i class="fas fa-arrow-up text-red-600"></i>
                                            @elseif($totalDiff < 0)
                                                <i class="fas fa-arrow-down text-green-600"></i>
                                            @endif
                                        @else
                                            <span class="text-gray-500">0</span>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Sale per Labour Hour -->
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">Sale per Labour Hour</td>
                                    @for($i = 0; $i < 7; $i++)
                                        <td class="px-3 py-3 text-sm text-center text-gray-900">
                                            @if(isset($avgOfHour[$depID][$i]) && $avgOfHour[$depID][$i] > 0)
                                                €{{ number_format($avgOfHour[$depID][$i], 2) }}
                                            @else
                                                €0.00
                                            @endif
                                        </td>
                                    @endfor
                                    <td class="px-3 py-3 text-sm text-center font-bold text-green-600">
                                        @if(isset($avgOfHour[$depID][7]) && $avgOfHour[$depID][7] > 0)
                                            €{{ number_format($avgOfHour[$depID][7], 2) }}
                                        @else
                                            €0.00
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            @endif

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Weekly Purchase Orders Chart -->
                @if(in_array('Ordering', $installedModuleNames) && Route::has('storeowner.ordering.get_allpo_chart_weekly'))
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Purchase Orders</h3>
                    <div id="bar_chart" style="width: 100%; height: 400px;"></div>
                </div>
                @endif

                <!-- Weekly Employee Hours Chart -->
                @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.get_hours_chart_weekly'))
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Employee Hours</h3>
                    <div id="bar_chart2" style="width: 100%; height: 400px;"></div>
                </div>
                @endif

                <!-- Weekly Sales Chart -->
                @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.get_sales_chart_weekly'))
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Sales Chart</h3>
                    <div id="bar_chart3" style="width: 100%; height: 400px;"></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Load Google Charts
        google.charts.load('current', {'packages':['bar']});
        
        // Dashboard Settings
        $(document).ready(function() {
            var settingsURL = '{{ route('storeowner.dashboard.settings') }}';
            var getSettingsURL = '{{ route('storeowner.dashboard.getSettings') }}';
            var firstTimeLoad = true;
            
            // Get current settings
            $.get(getSettingsURL, function(data) {
                if(data.status && data.data.length > 0) {
                    var value = data.data[0].sale_per_labour_hour;
                    $('input[name="sale_per_labour_hour"][value="' + value + '"]').prop('checked', true);
                }
            });
            
            // Update settings on change
            $('input[name="sale_per_labour_hour"]').on('change', function() {
                if(!firstTimeLoad) {
                    $.post(settingsURL, {
                        '_token': '{{ csrf_token() }}',
                        'value': $(this).val()
                    }, function(data) {
                        if(data.status) {
                            $("#weekNumberForm").submit();
                        }
                    });
                } else {
                    firstTimeLoad = false;
                }
            });
        });

        // Weekly Purchase Orders Chart
        @if(in_array('Ordering', $installedModuleNames) && Route::has('storeowner.ordering.get_allpo_chart_weekly'))
        google.charts.setOnLoadCallback(function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.ordering.get_allpo_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data1) {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Week');
                    data.addColumn('number', 'PO');
                    
                    var jsonData = JSON.parse(data1);
                    for (var i = 0; i < jsonData.length; i++) {
                        var weekLabel = "Week " + jsonData[i].week + " - " + jsonData[i].year;
                        data.addRow([weekLabel, parseInt(jsonData[i].total_amount || 0)]);
                    }
                    
                    var options = {
                        chart: {
                            title: 'Weekly Purchase Orders',
                        },
                        width: '100%',
                        height: 400,
                        axes: {
                            x: { 0: {side: 'top'} }
                        }
                    };
                    
                    var chart = new google.charts.Bar(document.getElementById('bar_chart'));
                    chart.draw(data, google.charts.Bar.convertOptions(options));
                }
            });
        });
        @endif

        // Weekly Employee Hours Chart
        @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.get_hours_chart_weekly'))
        google.charts.setOnLoadCallback(function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.clocktime.get_hours_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data2) {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Week');
                    data.addColumn('number', 'Total Hours');
                    
                    var jsonData = JSON.parse(data2);
                    for (var i = 0; i < jsonData.length; i++) {
                        data.addRow([jsonData[i].weekno, parseInt(jsonData[i].hours_worked || 0)]);
                    }
                    
                    var options = {
                        chart: {
                            title: 'Weekly Employee Hours',
                        },
                        width: '100%',
                        height: 400,
                        axes: {
                            x: { 0: {side: 'top'} }
                        }
                    };
                    
                    var chart = new google.charts.Bar(document.getElementById('bar_chart2'));
                    chart.draw(data, google.charts.Bar.convertOptions(options));
                }
            });
        });
        @endif

        // Weekly Sales Chart
        @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.get_sales_chart_weekly'))
        google.charts.setOnLoadCallback(function() {
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.dailyreport.get_sales_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data3) {
                    var data = new google.visualization.DataTable();
                    data.addColumn('string', 'Week');
                    data.addColumn('number', 'Total Sales');
                    
                    var jsonData = JSON.parse(data3);
                    for (var i = 0; i < jsonData.length; i++) {
                        data.addRow([jsonData[i].week, parseInt(jsonData[i].total_sell || 0)]);
                    }
                    
                    var options = {
                        chart: {
                            title: 'Weekly Sales Chart',
                        },
                        width: '100%',
                        height: 400,
                        axes: {
                            x: { 0: {side: 'top'} }
                        }
                    };
                    
                    var chart = new google.charts.Bar(document.getElementById('bar_chart3'));
                    chart.draw(data, google.charts.Bar.convertOptions(options));
                }
            });
        });
        @endif
    </script>
    @endpush
</x-storeowner-app-layout>
