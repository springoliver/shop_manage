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
                @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.clocked_in'))
                <div class="bg-teal-600 rounded-lg shadow p-4 text-white relative overflow-hidden">
                    <div class="absolute top-2 right-2">
                        <i class="fas fa-clock text-3xl opacity-20"></i>
                    </div>
                    <div class="relative z-10">
                        <a href="{{ route('storeowner.clocktime.clocked_in') }}" class="block">
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
                    <div id="bar_chart" class="w-full" style="height: 500px;"></div>
                </div>
                @endif

                <!-- Weekly Employee Hours Chart -->
                @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.get_hours_chart_weekly'))
                <div class="bg-white rounded-lg shadow p-6">
                    <div id="bar_chart2" class="w-full" style="height: 500px;"></div>
                </div>
                @endif

                <!-- Weekly Sales Chart -->
                @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.get_sales_chart_weekly'))
                <div class="bg-white rounded-lg shadow p-6">
                    <div id="bar_chart3" class="w-full" style="height: 500px;"></div>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Function to create bar chart using SVG and vanilla JavaScript - matching Employee dashboard style
        function createBarChart(containerId, title, data, yLabel) {
            const container = document.getElementById(containerId);
            if (!container) return;

            // Clear container
            container.innerHTML = '';

            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-10">No data available</p>';
                return;
            }

            const width = container.clientWidth || 740;
            const height = 500;
            const margin = { top: 40, right: 20, bottom: 80, left: 80 };
            const chartWidth = width - margin.left - margin.right;
            const chartHeight = height - margin.top - margin.bottom;

            // Find max value for scaling
            const maxValue = Math.max(...data.map(d => parseFloat(d.value) || 0));
            const yScale = maxValue > 0 ? chartHeight / maxValue : 1;

            // Create SVG
            const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('width', width);
            svg.setAttribute('height', height);
            svg.setAttribute('class', 'w-full');

            // Create group for chart
            const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            g.setAttribute('transform', `translate(${margin.left}, ${margin.top})`);

            // Title - positioned above chart
            const titleElement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            titleElement.setAttribute('x', chartWidth / 2);
            titleElement.setAttribute('y', -15);
            titleElement.setAttribute('text-anchor', 'middle');
            titleElement.setAttribute('class', 'text-base font-semibold fill-current text-gray-900');
            titleElement.textContent = title;
            g.appendChild(titleElement);

            // Y-axis label
            const yLabelElement = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            yLabelElement.setAttribute('x', -chartHeight / 2);
            yLabelElement.setAttribute('y', -50);
            yLabelElement.setAttribute('text-anchor', 'middle');
            yLabelElement.setAttribute('transform', 'rotate(-90)');
            yLabelElement.setAttribute('class', 'text-sm fill-current text-gray-600');
            yLabelElement.textContent = yLabel;
            g.appendChild(yLabelElement);

            // Calculate bar width - make bars thinner for better visibility with many data points
            const barCount = data.length;
            const maxBarWidth = 30; // Maximum bar width
            const minBarSpacing = 2; // Minimum spacing between bars
            const totalSpacing = barCount > 1 ? (barCount - 1) * minBarSpacing : 0;
            const availableWidth = chartWidth - totalSpacing;
            const calculatedBarWidth = Math.min(maxBarWidth, availableWidth / barCount);
            const barSpacing = barCount > 1 ? (chartWidth - (calculatedBarWidth * barCount)) / (barCount - 1) : 0;

            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.id = containerId + '_tooltip';
            tooltip.style.cssText = 'position: absolute; display: none; background: white; border: 1px solid #ccc; padding: 8px 12px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); pointer-events: none; z-index: 1000; font-size: 12px; white-space: nowrap;';
            
            // Ensure parent has position relative for tooltip positioning
            if (container.parentElement.style.position !== 'relative') {
                container.parentElement.style.position = 'relative';
            }
            container.parentElement.appendChild(tooltip);

            // Draw horizontal grid lines and Y-axis ticks
            const tickCount = 5;
            for (let i = 0; i <= tickCount; i++) {
                const tickValue = (maxValue / tickCount) * i;
                const tickY = chartHeight - (i * chartHeight / tickCount);

                // Grid line
                const gridLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                gridLine.setAttribute('x1', 0);
                gridLine.setAttribute('y1', tickY);
                gridLine.setAttribute('x2', chartWidth);
                gridLine.setAttribute('y2', tickY);
                gridLine.setAttribute('stroke', '#f3f4f6');
                gridLine.setAttribute('stroke-width', 1);
                g.appendChild(gridLine);

                // Tick line
                const tickLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                tickLine.setAttribute('x1', -5);
                tickLine.setAttribute('y1', tickY);
                tickLine.setAttribute('x2', 0);
                tickLine.setAttribute('y2', tickY);
                tickLine.setAttribute('stroke', '#9ca3af');
                tickLine.setAttribute('stroke-width', 1);
                g.appendChild(tickLine);

                // Tick label
                const tickLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                tickLabel.setAttribute('x', -10);
                tickLabel.setAttribute('y', tickY + 4);
                tickLabel.setAttribute('text-anchor', 'end');
                tickLabel.setAttribute('class', 'text-xs fill-current text-gray-600');
                tickLabel.textContent = tickValue.toFixed(2);
                g.appendChild(tickLabel);
            }

            // Calculate how many x-axis labels to show (every Nth bar to prevent overcrowding)
            const maxLabels = 25; // Maximum number of labels to show
            const labelInterval = Math.max(1, Math.floor(barCount / maxLabels));

            // Draw bars
            data.forEach((item, index) => {
                const barHeight = (parseFloat(item.value) || 0) * yScale;
                const x = index * (calculatedBarWidth + barSpacing);
                const y = chartHeight - barHeight;

                // Bar rectangle - light blue like Employee dashboard
                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', x);
                rect.setAttribute('y', y);
                rect.setAttribute('width', calculatedBarWidth);
                rect.setAttribute('height', barHeight);
                rect.setAttribute('fill', '#3b82f6'); // Blue-500
                rect.setAttribute('class', 'cursor-pointer');
                rect.setAttribute('data-value', item.value);
                rect.setAttribute('data-label', item.label);
                
                // Hover effect - show tooltip
                rect.addEventListener('mouseenter', function(e) {
                    tooltip.style.display = 'block';
                    tooltip.innerHTML = `
                        <div style="font-weight: 600; margin-bottom: 4px; color: #333;">${item.label}</div>
                        <div style="color: #3b82f6; font-weight: 600; margin-bottom: 4px;">${yLabel}</div>
                        <div style="font-size: 16px; font-weight: 600; color: #3b82f6;">${parseFloat(item.value).toFixed(2)}</div>
                    `;
                    
                    rect.setAttribute('fill', '#2563eb'); // Darker blue on hover
                    
                    // Force reflow to get tooltip dimensions
                    tooltip.offsetWidth;
                });
                
                rect.addEventListener('mousemove', function(e) {
                    // Position tooltip relative to mouse position
                    const containerRect = container.parentElement.getBoundingClientRect();
                    const mouseX = e.clientX - containerRect.left;
                    const mouseY = e.clientY - containerRect.top;
                    
                    // Position tooltip above and to the right of mouse, but adjust if needed
                    let tooltipX = mouseX + 10;
                    let tooltipY = mouseY - tooltip.offsetHeight - 10;
                    
                    // Adjust if tooltip goes off right edge
                    if (tooltipX + tooltip.offsetWidth > containerRect.width) {
                        tooltipX = mouseX - tooltip.offsetWidth - 10;
                    }
                    
                    // Adjust if tooltip goes off left edge
                    if (tooltipX < 0) {
                        tooltipX = 10;
                    }
                    
                    // Adjust if tooltip goes off top
                    if (tooltipY < 0) {
                        tooltipY = mouseY + 10;
                    }
                    
                    tooltip.style.left = tooltipX + 'px';
                    tooltip.style.top = tooltipY + 'px';
                });
                
                rect.addEventListener('mouseleave', function() {
                    tooltip.style.display = 'none';
                    rect.setAttribute('fill', '#3b82f6'); // Reset to original blue
                });
                
                g.appendChild(rect);

                // X-axis label - only show every Nth label to prevent overcrowding
                if (index % labelInterval === 0 || index === data.length - 1) {
                    const labelText = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    labelText.setAttribute('x', x + calculatedBarWidth / 2);
                    labelText.setAttribute('y', chartHeight + 20);
                    labelText.setAttribute('text-anchor', 'middle');
                    labelText.setAttribute('class', 'text-xs fill-current text-gray-600');
                    labelText.setAttribute('style', 'font-size: 11px;');
                    labelText.textContent = item.label;
                    g.appendChild(labelText);
                }
            });

            // Y-axis line
            const yAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            yAxis.setAttribute('x1', 0);
            yAxis.setAttribute('y1', 0);
            yAxis.setAttribute('x2', 0);
            yAxis.setAttribute('y2', chartHeight);
            yAxis.setAttribute('stroke', '#e5e7eb');
            yAxis.setAttribute('stroke-width', 2);
            g.appendChild(yAxis);

            // X-axis line
            const xAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            xAxis.setAttribute('x1', 0);
            xAxis.setAttribute('y1', chartHeight);
            xAxis.setAttribute('x2', chartWidth);
            xAxis.setAttribute('y2', chartHeight);
            xAxis.setAttribute('stroke', '#e5e7eb');
            xAxis.setAttribute('stroke-width', 2);
            g.appendChild(xAxis);

            svg.appendChild(g);
            container.appendChild(svg);
        }

        // Wait for jQuery and Vite bundle to be fully loaded
        (function() {
            var retries = 0;
            var maxRetries = 50; // 5 seconds max wait (50 * 100ms)
            
            function initDashboard() {
                // Check if jQuery is available
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initDashboard, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load after ' + maxRetries + ' retries');
                        return;
                    }
                }
                
                // Ensure DOM is ready
                $(document).ready(function() {
                    // Dashboard Settings
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

            // Weekly Purchase Orders Chart
            @if(in_array('Ordering', $installedModuleNames) && Route::has('storeowner.ordering.get_allpo_chart_weekly'))
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.ordering.get_allpo_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data1) {
                    try {
                        var jsonData = typeof data1 === 'string' ? JSON.parse(data1) : data1;
                        
                        const chartData = jsonData.map(function(item) {
                            return {
                                label: item.week.toString(),
                                value: parseFloat(item.total_amount || 0)
                            };
                        });
                        
                        createBarChart('bar_chart', 'Weekly Purchase Orders', chartData, 'Total Amount');
                    } catch (error) {
                        console.error('Error loading Weekly Purchase Orders chart:', error);
                        document.getElementById('bar_chart').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading Weekly Purchase Orders chart:', error);
                    document.getElementById('bar_chart').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                }
            });
            @endif

            // Weekly Employee Hours Chart
            @if(in_array('Clock in-out', $installedModuleNames) && Route::has('storeowner.clocktime.get_hours_chart_weekly'))
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.clocktime.get_hours_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data2) {
                    try {
                        var jsonData = typeof data2 === 'string' ? JSON.parse(data2) : data2;
                        
                        const chartData = jsonData.map(function(item) {
                            return {
                                label: item.weekno.toString(),
                                value: parseFloat(item.hours_worked || 0)
                            };
                        });
                        
                        createBarChart('bar_chart2', 'Weekly Employee Hours Chart', chartData, 'Total Hours');
                    } catch (error) {
                        console.error('Error loading Weekly Employee Hours chart:', error);
                        document.getElementById('bar_chart2').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading Weekly Employee Hours chart:', error);
                    document.getElementById('bar_chart2').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                }
            });
            @endif

            // Weekly Sales Chart
            @if(in_array('Daily Report', $installedModuleNames) && Route::has('storeowner.dailyreport.get_sales_chart_weekly'))
            $.ajax({
                type: 'POST',
                url: '{{ route('storeowner.dailyreport.get_sales_chart_weekly') }}',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data3) {
                    try {
                        var jsonData = typeof data3 === 'string' ? JSON.parse(data3) : data3;
                        
                        const chartData = jsonData.map(function(item) {
                            return {
                                label: item.week.toString(),
                                value: parseFloat(item.total_sell || 0)
                            };
                        });
                        
                        createBarChart('bar_chart3', 'Weekly Sales Chart', chartData, 'Total Sales');
                    } catch (error) {
                        console.error('Error loading Weekly Sales chart:', error);
                        document.getElementById('bar_chart3').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error loading Weekly Sales chart:', error);
                    document.getElementById('bar_chart3').innerHTML = '<p class="text-center text-red-500 py-8">Error loading chart data. Please try again.</p>';
                }
            });
            @endif
                }); // End of $(document).ready
            } // End of initDashboard function
            
            // Start initialization
            initDashboard();
        })(); // End of IIFE
    </script>
    @endpush
</x-storeowner-app-layout>
