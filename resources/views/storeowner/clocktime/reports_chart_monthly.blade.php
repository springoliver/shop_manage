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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Reports Chart</span>
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
                           class="px-4 py-2 block text-gray-600 hover:text-gray-800 hover:border-gray-300 border-b-2 border-transparent">
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
                           class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
                            Monthly Hours
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Chart View Tab -->
            <div class="bg-white rounded-lg shadow mb-6">
                <ul class="nav nav-tabs flex border-b border-gray-200" role="tablist">
                    <li class="nav-item">
                        <a href="{{ route('storeowner.clocktime.reports-chart-monthly') }}" 
                           class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
                            Monthly Chart View
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Chart Container -->
            <div class="bg-white rounded-lg shadow p-6">
                <div id="bar_chart" style="width: 100%; height: 500px;"></div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Wait for jQuery to be available
        (function() {
            var retries = 0;
            var maxRetries = 50;
            
            function initChart() {
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initChart, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load');
                        return;
                    }
                }
                
                $(document).ready(function() {
                    // Fetch chart data
                    fetch('{{ route('storeowner.clocktime.get_hours_chart_monthly') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        // Transform data for createBarChart function
                        const chartData = data.map(item => ({
                            label: item.mymonth.toString(),
                            value: parseFloat(item.hours_worked) || 0
                        }));
                        
                        // Use the same createBarChart function from dashboard
                        createBarChart('bar_chart', 'Company Performance - Monthly Total Employee Hours', chartData, 'Total Hours', '#3b82f6');
                    })
                    .catch(error => {
                        console.error('Error loading chart data:', error);
                        document.getElementById('bar_chart').innerHTML = '<p class="text-center text-gray-500 py-10">Error loading chart data. Please try again.</p>';
                    });
                });
            }
            
            initChart();
        })();

        // Function to create bar chart using SVG and vanilla JavaScript - matching CI style
        function createBarChart(containerId, title, data, yLabel, barColor = '#3b82f6') {
            const container = document.getElementById(containerId);
            if (!container) return;

            // Clear container
            container.innerHTML = '';

            if (!data || data.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-500 py-10">No data available</p>';
                return;
            }

            const width = container.clientWidth || 900;
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

            // Calculate bar width
            const barCount = data.length;
            const maxBarWidth = 30;
            const minBarSpacing = 2;
            const totalSpacing = barCount > 1 ? (barCount - 1) * minBarSpacing : 0;
            const availableWidth = chartWidth - totalSpacing;
            const calculatedBarWidth = Math.min(maxBarWidth, availableWidth / barCount);
            const barSpacing = barCount > 1 ? (chartWidth - (calculatedBarWidth * barCount)) / (barCount - 1) : 0;

            // Create tooltip element
            const tooltip = document.createElement('div');
            tooltip.id = containerId + '_tooltip';
            tooltip.style.cssText = 'position: absolute; display: none; background: white; border: 1px solid #ccc; padding: 8px 12px; border-radius: 4px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); pointer-events: none; z-index: 1000; font-size: 12px; white-space: nowrap;';
            
            if (container.parentElement.style.position !== 'relative') {
                container.parentElement.style.position = 'relative';
            }
            container.parentElement.appendChild(tooltip);

            // Draw horizontal grid lines and Y-axis ticks
            const tickCount = 5;
            for (let i = 0; i <= tickCount; i++) {
                const tickValue = (maxValue / tickCount) * i;
                const tickY = chartHeight - (i * chartHeight / tickCount);

                const gridLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                gridLine.setAttribute('x1', 0);
                gridLine.setAttribute('y1', tickY);
                gridLine.setAttribute('x2', chartWidth);
                gridLine.setAttribute('y2', tickY);
                gridLine.setAttribute('stroke', '#f3f4f6');
                gridLine.setAttribute('stroke-width', 1);
                g.appendChild(gridLine);

                const tickLine = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                tickLine.setAttribute('x1', -5);
                tickLine.setAttribute('y1', tickY);
                tickLine.setAttribute('x2', 0);
                tickLine.setAttribute('y2', tickY);
                tickLine.setAttribute('stroke', '#9ca3af');
                tickLine.setAttribute('stroke-width', 1);
                g.appendChild(tickLine);

                const tickLabel = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                tickLabel.setAttribute('x', -10);
                tickLabel.setAttribute('y', tickY + 4);
                tickLabel.setAttribute('text-anchor', 'end');
                tickLabel.setAttribute('class', 'text-xs fill-current text-gray-600');
                tickLabel.textContent = tickValue.toFixed(2);
                g.appendChild(tickLabel);
            }

            const maxLabels = 25;
            const labelInterval = Math.max(1, Math.floor(barCount / maxLabels));

            // Draw bars
            data.forEach((item, index) => {
                const barHeight = (parseFloat(item.value) || 0) * yScale;
                const x = index * (calculatedBarWidth + barSpacing);
                const y = chartHeight - barHeight;

                const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
                rect.setAttribute('x', x);
                rect.setAttribute('y', y);
                rect.setAttribute('width', calculatedBarWidth);
                rect.setAttribute('height', barHeight);
                rect.setAttribute('fill', barColor);
                rect.setAttribute('class', 'cursor-pointer');
                rect.setAttribute('data-value', item.value);
                rect.setAttribute('data-label', item.label);
                
                rect.addEventListener('mouseenter', function(e) {
                    tooltip.style.display = 'block';
                    tooltip.innerHTML = `
                        <div style="font-weight: 600; margin-bottom: 4px; color: #333;">${item.label}</div>
                        <div style="color: ${barColor}; font-weight: 600; margin-bottom: 4px;">${yLabel}</div>
                        <div style="font-size: 16px; font-weight: 600; color: ${barColor};">${parseFloat(item.value).toFixed(2)}</div>
                    `;
                    
                    rect.setAttribute('fill', '#2563eb'); // Darker blue on hover
                    
                    tooltip.offsetWidth;
                });
                
                rect.addEventListener('mousemove', function(e) {
                    const containerRect = container.parentElement.getBoundingClientRect();
                    const mouseX = e.clientX - containerRect.left;
                    const mouseY = e.clientY - containerRect.top;
                    
                    let tooltipX = mouseX + 10;
                    let tooltipY = mouseY - tooltip.offsetHeight - 10;
                    
                    if (tooltipX + tooltip.offsetWidth > containerRect.width) {
                        tooltipX = mouseX - tooltip.offsetWidth - 10;
                    }
                    
                    if (tooltipX < 0) {
                        tooltipX = 10;
                    }
                    
                    if (tooltipY < 0) {
                        tooltipY = mouseY + 10;
                    }
                    
                    tooltip.style.left = tooltipX + 'px';
                    tooltip.style.top = tooltipY + 'px';
                });
                
                rect.addEventListener('mouseleave', function() {
                    tooltip.style.display = 'none';
                    rect.setAttribute('fill', barColor); // Reset to original color
                });
                
                g.appendChild(rect);

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

            const yAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            yAxis.setAttribute('x1', 0);
            yAxis.setAttribute('y1', 0);
            yAxis.setAttribute('x2', 0);
            yAxis.setAttribute('y2', chartHeight);
            yAxis.setAttribute('stroke', '#e5e7eb');
            yAxis.setAttribute('stroke-width', 2);
            g.appendChild(yAxis);

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
    </script>
    @endpush
</x-storeowner-app-layout>

