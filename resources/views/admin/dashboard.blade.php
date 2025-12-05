@section('page_header', 'Dashboard')

<x-admin-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Dashboard</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Dashboard</h1>
    </div>

    <!-- Employee Statistics -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Employee Statistics</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Store Owners Box -->
            <a href="{{ route('admin.store-owners.index') }}" title="All Store Owners" class="block">
                <div class="flex bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 relative overflow-hidden">
                    <div class="top-4 left-4 text-white opacity-20">
                        <i class="fas fa-users text-4xl"></i>
                    </div>
                    <div class="relative z-10 ml-[20px]">
                        <h5 class="text-white text-sm font-medium uppercase mb-2">All Store Owners</h5>
                        <p class="text-white text-3xl font-bold">{{ $statistics['owner_count'] }}</p>
                    </div>
                </div>
            </a>

            <!-- User Groups Box -->
            <a href="{{ route('admin.user-groups.index') }}" title="All User Groups" class="block">
                <div class="flex bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 relative overflow-hidden">
                    <div class="top-4 left-4 text-white opacity-20">
                        <i class="fas fa-user text-4xl"></i>
                    </div>
                    <div class="relative z-10 ml-[20px]">
                        <h5 class="text-white text-sm font-medium uppercase mb-2">All User Groups</h5>
                        <p class="text-white text-3xl font-bold">{{ $statistics['usergroup_count'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Categories Box -->
            <a href="{{ route('admin.store-types.index') }}" title="All Categories" class="block">
                <div class="flex bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 relative overflow-hidden">
                    <div class="top-4 left-4 text-white opacity-20">
                        <i class="fas fa-list text-4xl"></i>
                    </div>
                    <div class="relative z-10 ml-[20px]">
                        <h5 class="text-white text-sm font-medium uppercase mb-2">All Categories</h5>
                        <p class="text-white text-3xl font-bold">{{ $statistics['category_count'] }}</p>
                    </div>
                </div>
            </a>

            <!-- Departments Box -->
            <a href="{{ route('admin.departments.index') }}" title="All Departments" class="block">
                <div class="flex bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 p-6 relative overflow-hidden">
                    <div class="top-4 left-4 text-white opacity-20">
                        <i class="fas fa-university text-4xl"></i>
                    </div>
                    <div class="relative z-10 ml-[20px]">
                        <h5 class="text-white text-sm font-medium uppercase mb-2">Departments</h5>
                        <p class="text-white text-3xl font-bold">{{ $statistics['department_count'] }}</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Graphs Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Owner Graph -->
        <div class="bg-white rounded-lg shadow-md p-6 border-r border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <div class="flex space-x-2">
                    <a href="{{ route('admin.dashboard.owner', 'weekly') }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md {{ (!isset($utype) || $utype == '' || $utype == 'weekly') ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Week
                    </a>
                    <a href="{{ route('admin.dashboard.owner', 'monthly') }}" 
                       class="px-4 py-2 text-sm font-medium rounded-md {{ (isset($utype) && $utype == 'monthly') ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        Monthly
                    </a>
                </div>
            </div>

            @php
                $week = '';
                $month = '';
                if (!isset($utype) || $utype == '' || $utype == 'weekly') {
                    $week = 'active';
                }
                if (isset($utype) && $utype == 'monthly') {
                    $month = 'active';
                }
                
                $minimumyear = $mindate ? date('Y', strtotime($mindate)) : date('Y');
                $currentyear = date('Y');
                $diff = $currentyear - $minimumyear;
                $maximumyear = $minimumyear + $diff;
            @endphp

            <form method="POST" action="{{ route('admin.dashboard.owner', ['utype' => 'monthyear']) }}" class="mb-4">
                @csrf
                <div class="flex items-end space-x-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Year</label>
                        <select name="syear" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                            <option value="">All</option>
                            @for($i = 0; $i <= $diff; $i++)
                                <option value="{{ $maximumyear - $i }}" {{ (isset($syear) && $syear == ($maximumyear - $i)) ? 'selected' : '' }}>
                                    {{ $maximumyear - $i }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Month</label>
                        <select name="smonth" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                            <option value="">All</option>
                            @for($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (isset($smonth) && $smonth == $i) ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Search
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">
                    @if((!isset($utype) || $utype == '' || $utype == 'monthly') && !isset($syear) && !isset($smonth))
                        {{ date('Y') }} - {{ date('F') }}
                    @elseif(isset($utype) && $utype == 'monthyear')
                        @if(isset($syear) && $syear != '' && isset($smonth) && $smonth != '')
                            {{ $syear }} - {{ date('F', mktime(0, 0, 0, $smonth, 1)) }}
                        @elseif((!isset($syear) || $syear == '') && isset($smonth) && $smonth != '')
                            {{ date('Y') }} - {{ date('F', mktime(0, 0, 0, $smonth, 1)) }}
                        @elseif(isset($syear) && $syear != '')
                            {{ $syear }}
                        @endif
                    @else
                        {{ date('Y') }} - {{ date('F') }}
                    @endif
                </h3>
            </div>

            <div id="chart_div" style="width: 100%; height: 300px;"></div>
        </div>

        <!-- Module Activity Graph (Placeholder) -->
        <div class="bg-white rounded-lg shadow-md p-6 border-b border-gray-200">
            <div class="text-center py-12">
                <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Module Activity Graph</p>
                <p class="text-sm text-gray-400 mt-2">Will be available after paid_module table is implemented</p>
            </div>
        </div>

        <!-- Revenue Graph (Placeholder) -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t border-gray-200">
            <div class="text-center py-12">
                <i class="fas fa-chart-area text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">Revenue Graph</p>
                <p class="text-sm text-gray-400 mt-2">Will be available after paid_module table is implemented</p>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = google.visualization.arrayToDataTable([
                ['Date', 'Owners'],
                @foreach($chartData as $item)
                    ['{{ $item['label'] }}', {{ $item['value'] }}],
                @endforeach
            ]);

            var options = {
                title: 'Owners',
                vAxis: {title: 'Total Owners', titleTextStyle: {color: 'red'}},
                hAxis: {
                    slantedText: true,
                    slantedTextAngle: 65
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }
    </script>
    @endpush
</x-admin-app-layout>
