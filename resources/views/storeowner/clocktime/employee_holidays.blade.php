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

            <!-- Search and Per Page Controls -->
            <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <input type="text" 
                           id="searchbox"
                           placeholder="Search holidays..." 
                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                </div>
                
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-2">
                        <label class="text-sm text-gray-700">Show:</label>
                        <select id="perPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                        <span class="text-sm text-gray-700">entries</span>
                    </div>
                    <div>
                        <a href="{{ route('storeowner.clocktime.export-all-employee-hols') }}" 
                           class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 text-sm">
                            Export holiday summary
                        </a>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="holiday-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="year" style="cursor: pointer;">
                                        Year <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="employee" style="cursor: pointer;">
                                        Employee Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="salary" style="cursor: pointer;">
                                        Salary Method <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="hours" style="cursor: pointer;">
                                        Hours Worked <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="due" style="cursor: pointer;">
                                        Due Holidays <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="extras" style="cursor: pointer;">
                                        Extras <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="taken" style="cursor: pointer;">
                                        Holidays Taken <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="remaining" style="cursor: pointer;">
                                        Holidays Remaining <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="holidayTableBody">
                                @if($empPayrollHrs->count() > 0)
                                    @foreach($empPayrollHrs as $payroll)
                                        @php
                                            $dueHolidays = $payroll->sallary_method == 'hourly' 
                                                ? floor((float)$payroll->holiday_calculated) 
                                                : (float)$payroll->holiday_days_counted;
                                            $holidaysTaken = $payroll->sallary_method == 'hourly' 
                                                ? (float)$payroll->holiday_hrs 
                                                : (float)$payroll->holiday_days;
                                            $holidaysRemaining = $payroll->sallary_method == 'hourly' 
                                                ? (float)$payroll->holiday_calculated - (float)$payroll->holiday_hrs 
                                                : (float)$payroll->holiday_days_counted - (float)$payroll->holiday_days;
                                        @endphp
                                        <tr class="holiday-row hover:bg-gray-50" 
                                            data-row-index="{{ $loop->index }}"
                                            data-year="{{ $payroll->year }}"
                                            data-employee="{{ strtolower($payroll->firstname . ' ' . $payroll->lastname) }}"
                                            data-salary="{{ strtolower($payroll->sallary_method) }}"
                                            data-hours="{{ (float)$payroll->hours_worked }}"
                                            data-due="{{ $dueHolidays }}"
                                            data-extras="{{ (float)$payroll->extra_holiday_calculated }}"
                                            data-taken="{{ $holidaysTaken }}"
                                            data-remaining="{{ $holidaysRemaining }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('storeowner.clocktime.group-yearly-hrs-all-employee', ['year' => base64_encode($payroll->year)]) }}" class="text-blue-600 hover:text-blue-800" title="Year">
                                                    {{ $payroll->year }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800" title="Employee">
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
                                    <tr id="noHolidayRow">
                                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No records found.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Client-side Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $empPayrollHrs->count() }}</span> entries
                        </div>
                        <div id="paginationControls" class="flex items-center gap-2">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Client-side pagination, search, and sorting
        let currentPage = 1;
        let perPage = 10;
        let allRows = [];
        let filteredRows = [];
        let sortColumn = null;
        let sortDirection = 'asc';

        function initializePagination() {
            const tbody = document.getElementById('holidayTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.holiday-row'));
            filteredRows = [...allRows];
            
            const noHolidayRow = document.getElementById('noHolidayRow');
            if (noHolidayRow && allRows.length > 0) {
                noHolidayRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            const tbody = document.getElementById('holidayTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.holiday-row'));
            
            const searchTerm = document.getElementById('searchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                filteredRows = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                filteredRows = [...allRows];
            }

            if (sortColumn) {
                filteredRows.sort((a, b) => {
                    let aValue = a.getAttribute(`data-${sortColumn}`) || '';
                    let bValue = b.getAttribute(`data-${sortColumn}`) || '';
                    
                    // Handle numeric sorting for numeric columns
                    if (['year', 'hours', 'due', 'extras', 'taken', 'remaining'].includes(sortColumn)) {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                        return sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
                    }
                    
                    // String comparison for other columns
                    if (aValue < bValue) {
                        return sortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return sortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(filteredRows.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, filteredRows.length);

            if (sortColumn && filteredRows.length > 0) {
                const noHolidayRow = document.getElementById('noHolidayRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noHolidayRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noHolidayRow') {
                        if (noHolidayRow && noHolidayRow.parentNode) {
                            tbody.insertBefore(row, noHolidayRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.holiday-row'));
                const sortedFilteredIndices = filteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                allRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                filteredRows = newFilteredRows;
            }

            allRows.forEach(row => {
                if (row.id !== 'noHolidayRow') {
                    row.style.display = 'none';
                }
            });

            const noHolidayRow = document.getElementById('noHolidayRow');
            if (noHolidayRow) {
                if (filteredRows.length === 0) {
                    noHolidayRow.style.display = '';
                } else {
                    noHolidayRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noHolidayRow') {
                    filteredRows[i].style.display = '';
                }
            }

            document.getElementById('showingStart').textContent = filteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('totalEntries').textContent = filteredRows.length;

            generatePaginationControls(totalPages);
        }

        function generatePaginationControls(totalPages) {
            const paginationDiv = document.getElementById('paginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (currentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = currentPage === 1;
            prevBtn.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    currentPage = 1;
                    updateDisplay();
                };
                paginationDiv.appendChild(firstBtn);

                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.textContent = '...';
                    ellipsis.className = 'px-2';
                    paginationDiv.appendChild(ellipsis);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                const pageBtn = document.createElement('button');
                pageBtn.textContent = i;
                pageBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md ' + 
                    (i === currentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    currentPage = i;
                    updateDisplay();
                };
                paginationDiv.appendChild(pageBtn);
            }

            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.textContent = '...';
                    ellipsis.className = 'px-2';
                    paginationDiv.appendChild(ellipsis);
                }

                const lastBtn = document.createElement('button');
                lastBtn.textContent = totalPages;
                lastBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                lastBtn.onclick = () => {
                    currentPage = totalPages;
                    updateDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (currentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            document.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = document.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = sortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            currentPage = 1;
            updateDisplay();
        }

        document.addEventListener('DOMContentLoaded', function() {
            initializePagination();

            document.getElementById('searchbox')?.addEventListener('keyup', function() {
                currentPage = 1;
                updateDisplay();
            });

            document.getElementById('perPageSelect')?.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                updateDisplay();
            });

            document.querySelectorAll('.sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.getAttribute('data-sort');
                    if (column) {
                        sortTable(column);
                    }
                });
            });
        });
    </script>
    @endpush
</x-storeowner-app-layout>

