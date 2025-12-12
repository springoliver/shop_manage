<x-employee-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
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

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Page Title -->
            <h1 class="text-2xl font-semibold text-gray-900 mb-6">Dashboard</h1>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
                <!-- Due Employee Reviews -->
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-gray-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Due Employee Reviews</span>
                    </div>
                    <div class="text-2xl font-bold text-green-600">{{ $employeereviewsDueCount ?? 0 }}</div>
                </div>

                <!-- Time-off requests -->
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-gray-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Time-off requests</span>
                    </div>
                    <a href="{{ route('employee.holidayrequest.index') }}" class="block">
                        <div class="text-2xl font-bold text-green-600">{{ $holidayrequestCount ?? 0 }}</div>
                    </a>
                    <a href="{{ route('employee.holidayrequest.index') }}" class="block">
                        <span class="text-xs text-blue-600">Pending Approval {{ $holidayrequestPendingCount ?? 0 }}</span>
                    </a>
                </div>

                <!-- Resignation -->
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-gray-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Resignation</span>
                    </div>
                    <a href="{{ route('employee.resignation.index') }}" class="block">
                        <div class="text-2xl font-bold text-green-600">{{ $resignationCount ?? 0 }}</div>
                    </a>
                    <a href="{{ route('employee.resignation.index') }}" class="block">
                        <span class="text-xs text-blue-600">Pending Resignation {{ $resignationPendingCount ?? 0 }}</span>
                    </a>
                </div>

                <!-- Currently Clocked-in -->
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-user text-gray-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Currently Clocked-in</span>
                    </div>
                    <a href="#" class="block">
                        <div class="text-2xl font-bold text-red-600">{{ $clockInCount ?? 0 }}</div>
                    </a>
                </div>

                <!-- Awaiting Delivery Dockets -->
                <div class="bg-white rounded-lg shadow p-4 border border-gray-200">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-file-alt text-gray-600 mr-2"></i>
                        <span class="text-sm font-medium text-gray-700">Awaiting Delivery Dockets</span>
                    </div>
                    <a href="#" class="block">
                        <div class="text-2xl font-bold text-red-600">{{ $deliveryDocketsCount ?? 0 }}</div>
                    </a>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Weekly Employee Hours Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Weekly Employee Hours Chart</h3>
                        <div id="bar_chart2" class="w-full" style="height: 500px;"></div>
                    </div>
                </div>

                <!-- Weekly Purchase Orders Chart -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Weekly Purchase Orders</h3>
                        <div id="bar_chart3" class="w-full" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Function to create bar chart using SVG and vanilla JavaScript - matching CI style
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
            // Show labels more frequently but ensure they don't overlap
            const maxLabels = 25; // Maximum number of labels to show
            const labelInterval = Math.max(1, Math.floor(barCount / maxLabels));

            // Draw bars
            data.forEach((item, index) => {
                const barHeight = (parseFloat(item.value) || 0) * yScale;
                const x = index * (calculatedBarWidth + barSpacing);
                const y = chartHeight - barHeight;

                // Bar rectangle - light blue like CI
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
                        <div style="color: #3b82f6; font-weight: 600; margin-bottom: 4px;">Total Hours</div>
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

        // Load charts when page is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch and display Weekly Employee Hours Chart
            fetch('{{ route("employee.dashboard.get-hours-chart-weekly") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const chartData = data.map(item => ({
                    label: item.weekno.toString(),
                    value: item.hours_worked
                }));
                createBarChart('bar_chart2', 'Weekly Employee Hours Chart', chartData, 'Total Hours');
            })
            .catch(error => {
                console.error('Error loading hours chart:', error);
                document.getElementById('bar_chart2').innerHTML = '<p class="text-center text-red-500 py-10">Error loading chart data</p>';
            });

            // Fetch and display Weekly Purchase Orders Chart
            fetch('{{ route("employee.dashboard.get-po-chart-weekly") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const chartData = data.map(item => ({
                    label: item.week.toString(),
                    value: item.total_amount
                }));
                createBarChart('bar_chart3', 'Weekly Purchase Orders', chartData, 'Total Amount');
            })
            .catch(error => {
                console.error('Error loading PO chart:', error);
                document.getElementById('bar_chart3').innerHTML = '<p class="text-center text-red-500 py-10">Error loading chart data</p>';
            });
        });
    </script>
    @endpush
</x-employee-app-layout>
