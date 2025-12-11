@section('page_header', 'PO Reports Charts - Weekly')
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
                        <a href="{{ route('storeowner.ordering.index') }}" class="ml-1 hover:text-gray-700">Ordering</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">PO Reports Charts</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            <div class="mb-4">
                <ul class="flex space-x-2 border-b border-gray-200 mb-4">
                    <li>
                        <a href="{{ route('storeowner.ordering.report') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Purchase orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.product_report') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Purchased Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.missing_delivery_dockets') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Delivery Dockets
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.credit_notes') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Credit Notes
                        </a>
                    </li>
                </ul>
                <ul class="flex space-x-2 border-b border-gray-200">
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_yearly') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Yearly Orders Chart View
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_monthly') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Monthly Orders Chart View
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_weekly') }}" 
                           class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                            Weekly Orders Chart View
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Chart Container -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-2">Company Performance</h3>
                <p class="text-sm text-gray-600 mb-4">Weekly Total Orders</p>
                <div id="bar_chart" class="w-full" style="min-height: 500px;"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        async function loadChart() {
            try {
                const response = await fetch('{{ route("storeowner.ordering.get_po_chart_weekly") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error('Failed to load chart data');
                }

                const jsonData = await response.json();

                if (!jsonData || jsonData.length === 0) {
                    document.getElementById('bar_chart').innerHTML = '<p class="text-center text-gray-500">No data available</p>';
                    return;
                }

                // Find max value for scaling
                const maxValue = Math.max(...jsonData.map(item => item.total_amount || 0));
                if (maxValue === 0) {
                    document.getElementById('bar_chart').innerHTML = '<p class="text-center text-gray-500">No data available</p>';
                    return;
                }

                // Chart dimensions
                const chartWidth = 900;
                const chartHeight = 500;
                const padding = { top: 60, right: 40, bottom: 80, left: 80 };
                const barAreaWidth = chartWidth - padding.left - padding.right;
                const barAreaHeight = chartHeight - padding.top - padding.bottom;
                const barWidth = Math.max(15, Math.floor(barAreaWidth / jsonData.length) - 3);

                // Build SVG chart (matching Google Charts style)
                let svgHTML = `
                    <svg width="${chartWidth}" height="${chartHeight}" style="overflow: visible;">
                        <!-- Y-axis -->
                        <line x1="${padding.left}" y1="${padding.top}" x2="${padding.left}" y2="${padding.top + barAreaHeight}" stroke="#666" stroke-width="1"/>
                        <!-- X-axis -->
                        <line x1="${padding.left}" y1="${padding.top + barAreaHeight}" x2="${padding.left + barAreaWidth}" y2="${padding.top + barAreaHeight}" stroke="#666" stroke-width="1"/>
                        
                        <!-- Y-axis labels and grid lines -->
                        <text x="${padding.left - 10}" y="${padding.top + barAreaHeight + 20}" text-anchor="middle" font-size="12" fill="#333">0</text>
                `;

                // Y-axis scale (0 to maxValue)
                const ySteps = Math.max(8, Math.ceil(maxValue));
                const stepValue = Math.ceil(maxValue / 8);
                for (let i = 0; i <= 8; i++) {
                    const value = stepValue * i;
                    const y = padding.top + barAreaHeight - (i / 8) * barAreaHeight;
                    svgHTML += `
                        <line x1="${padding.left}" y1="${y}" x2="${padding.left + barAreaWidth}" y2="${y}" stroke="#e0e0e0" stroke-width="1"/>
                        <text x="${padding.left - 15}" y="${y + 4}" text-anchor="end" font-size="11" fill="#666">${value}</text>
                    `;
                }

                // X-axis label
                svgHTML += `<text x="${padding.left + barAreaWidth / 2}" y="${chartHeight - 10}" text-anchor="middle" font-size="12" fill="#333">Week</text>`;

                // Bars
                jsonData.forEach((item, index) => {
                    const barHeight = (item.total_amount / maxValue) * barAreaHeight;
                    const x = padding.left + (index * (barAreaWidth / jsonData.length)) + (barAreaWidth / jsonData.length - barWidth) / 2;
                    const y = padding.top + barAreaHeight - barHeight;
                    const weekLabel = item.week || 'null';
                    
                    svgHTML += `
                        <!-- Bar -->
                        <rect x="${x}" y="${y}" width="${barWidth}" height="${barHeight}" 
                              fill="#4285f4" stroke="none" class="bar-hover" 
                              data-week="${weekLabel}" data-value="${item.total_amount}">
                            <title>Week ${weekLabel}: €${item.total_amount.toFixed(2)}</title>
                        </rect>
                        <!-- Week label -->
                        <text x="${x + barWidth / 2}" y="${padding.top + barAreaHeight + 35}" 
                              text-anchor="middle" font-size="11" fill="#333">${weekLabel}</text>
                        <!-- Value label on top -->
                        <text x="${x + barWidth / 2}" y="${y - 5}" 
                              text-anchor="middle" font-size="11" fill="#333">${weekLabel || 'null'}</text>
                    `;
                });

                svgHTML += '</svg>';

                document.getElementById('bar_chart').innerHTML = svgHTML;
            } catch (error) {
                console.error('Error loading chart:', error);
                document.getElementById('bar_chart').innerHTML = '<p class="text-center text-red-500">Error loading chart data. Please try again.</p>';
            }
        }

        document.addEventListener('DOMContentLoaded', loadChart);
    </script>
    @endpush
</x-storeowner-app-layout>

