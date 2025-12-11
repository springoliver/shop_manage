@section('page_header', 'Reports Charts - Monthly All')
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
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Yearly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_monthly') }}" 
                       class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
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
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Company Performance - Purchases</h3>
                <div id="bar_chart" class="mt-6">
                    <div class="text-center text-gray-500 py-8">Loading chart data...</div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetch('{{ route('storeowner.ordering.get_allreports_chart_monthly') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                drawChart(data);
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('bar_chart').innerHTML = '<div class="text-red-500 text-center py-8">Error loading chart data</div>';
            });

            function drawChart(data) {
                if (!data || data.length === 0) {
                    document.getElementById('bar_chart').innerHTML = '<div class="text-gray-500 text-center py-8">No data available</div>';
                    return;
                }

                // Find max value for scaling
                const maxValue = Math.max(...data.map(item => item.total_amount || 0));

                // Create chart HTML
                let chartHTML = '<div class="space-y-4">';
                
                data.forEach(item => {
                    const percentage = maxValue > 0 ? (item.total_amount / maxValue) * 100 : 0;
                    const barWidth = Math.max(percentage, 2); // Minimum 2% width for visibility
                    const label = item.month + ' ' + item.year;
                    
                    chartHTML += `
                        <div class="flex items-center">
                            <div class="w-32 text-sm font-medium text-gray-700 mr-4 text-right">${label}</div>
                            <div class="flex-1 relative">
                                <div class="bg-gray-200 rounded-full h-8 flex items-center">
                                    <div class="bg-green-600 h-8 rounded-full flex items-center justify-end pr-2" style="width: ${barWidth}%; min-width: 2%;">
                                        <span class="text-white text-xs font-medium">€${parseFloat(item.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                chartHTML += '</div>';
                document.getElementById('bar_chart').innerHTML = chartHTML;
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>
