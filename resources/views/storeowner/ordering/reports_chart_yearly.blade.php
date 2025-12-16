@section('page_header', 'Reports Charts - Yearly All')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Report Charts</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            <div class="mb-4">
                <div class="flex space-x-2 border-b border-gray-200 mb-4">
                    <a href="{{ route('storeowner.ordering.tax_analysis') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Tax Analysis
                    </a>
                    <a href="{{ route('storeowner.ordering.add_invoice') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Add Bills
                    </a>
                </div>
                <div class="flex space-x-2 border-b border-gray-200">
                    <a href="{{ route('storeowner.ordering.reports_chart_yearly') }}" 
                       class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                        Yearly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_monthly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Monthly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_weekly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Weekly Chart View
                    </a>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="bg-white rounded-lg shadow p-6">
                <div id="bar_chart" style="width: 900px; height: 500px;"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load the Visualization API and the bar package.
        google.charts.load('current', {'packages':['bar']});
        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            fetch('{{ route('storeowner.ordering.get_allreports_chart_yearly') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data1 => {
                // Create our data table out of JSON data loaded from server.
                var data = new google.visualization.DataTable();

                data.addColumn('string', 'Year');
                data.addColumn('number', 'Total');

                var jsonData = data1;
                
                for (var i = 0; i < jsonData.length; i++) {
                    data.addRow([String(jsonData[i].myyear), parseInt(jsonData[i].mytotal_amount)]);
                }

                var options = {
                    chart: {
                        title: 'Company Performance',
                        subtitle: 'Yaerly All Expense'
                    },
                    width: 900,
                    height: 500,
                    axes: {
                        x: {
                            0: {side: 'top'}
                        }
                    }
                };

                var chart = new google.charts.Bar(document.getElementById('bar_chart'));
                chart.draw(data, options);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('bar_chart').innerHTML = '<div class="text-red-500 text-center py-8">Error loading chart data</div>';
            });
        }
    </script>
    @endpush
</x-storeowner-app-layout>
