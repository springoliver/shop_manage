@section('page_header', 'Rosters Template')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Rosters Template</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Navigation Buttons -->
    <div class="mb-2 flex flex-wrap gap-2">
        <a href="{{ route('storeowner.roster.index') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Roster Template
        </a>
        <a href="{{ route('storeowner.roster.viewweekroster') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Current Roster
        </a>
        <form action="{{ route('storeowner.roster.searchweekroster') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="dateofbirth" value="{{ date('Y-m-d') }}">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                Search & Edit
            </button>
        </form>
        <a href="javascript:void(0);" onclick="document.getElementById('searchPrintForm').submit();" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Search & Print
        </a>
        <form id="searchPrintForm" action="{{ route('storeowner.roster.searchprintroster') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="dateofbirth" value="{{ date('Y-m-d') }}">
        </form>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-2">
        <!-- Department Filter Buttons -->
        @if($departments->count() > 0)
            <div class="mb-4 flex flex-wrap gap-2">
                @foreach($departments as $dept)
                    <a href="{{ route('storeowner.roster.index-dept', base64_encode($dept->departmentid)) }}" 
                    class="px-4 py-2 {{ $departmentid == $dept->departmentid ? 'bg-blue-700' : 'bg-blue-600' }} text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        {{ $dept->department }} Roster
                    </a>
                @endforeach
            </div>
        @endif

        <div class="flex space-x-3">
            <a href="{{ route('storeowner.roster.weekroster') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                <i class="fas fa-plus mr-2"></i>
                Add Week Roster
            </a>
        </div>
    </div>

    <!-- Add Roster Form -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Add Roster</h3>
            <form action="{{ route('storeowner.roster.store') }}" method="POST" id="addRosterForm">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <!-- <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Select</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sunday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tuesday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wednesday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thursday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Friday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saturday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead> -->
                        <tbody>
                            <tr>
                                <td class="px-1 py-3">
                                    <select id="employeeid_select" name="employeeid" class="w-full border border-gray-300 rounded-md px-1 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                        <option value="0">Select</option>
                                        @foreach($employeesForForm as $emp)
                                            <option value="{{ $emp->employeeid }}">{{ $emp->firstname }} {{ $emp->lastname }}</option>
                                        @endforeach
                                    </select>
                                    @foreach($employeesForForm as $emp)
                                        <input type="hidden" id="roster_week_hrs_{{ $emp->employeeid }}" value="{{ $emp->roster_week_hrs }}" />
                                        <input type="hidden" id="roster_day_hrs_{{ $emp->employeeid }}" value="{{ $emp->roster_day_hrs }}" />
                                    @endforeach
                                </td>
                                @php
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $startTime = strtotime('10:30');
                                    $endTime = strtotime('23:30');
                                    $timeOptions = [];
                                    $current = $startTime;
                                    while ($current <= $endTime) {
                                        $timeOptions[] = date('H:i:s', $current);
                                        $current = strtotime('+30 minutes', $current);
                                    }
                                @endphp
                                @foreach($days as $day)
                                    <td class="px-4 py-3">
                                        <select name="{{ $day }}_start" id="{{ $day }}_start" class="w-full mb-1 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off">Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}">{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                        <select name="{{ $day }}_end" id="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off">Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}">{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-medium" id="btnsubmit1">
                                        Save
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <input type="hidden" name="minute" id="minute" value="0">
                <input type="hidden" name="hour" id="hour" value="0">
                <input type="hidden" name="total_hour" id="total_hour" value="0">
                <input type="hidden" name="total_minute" id="total_minute" value="0">
            </form>
        </div>
    </div>

    <!-- Search and Per Page Controls -->
    <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <input type="text" 
                   id="searchbox"
                   placeholder="Search rosters..." 
                   class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
        </div>
        
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
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="roster-table">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sortable" data-sort="employee" style="cursor: pointer;">
                                Employee <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sun</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mon</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fri</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sat</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase sortable" data-sort="total" style="cursor: pointer;">
                                Total <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="rosterTableBody">
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @forelse($employeedata as $employee)
                            @php
                                $employeeRosters = $weekroster->where('employeeid', $employee->employeeid);
                                $totalHours = 0;
                                $rosterByDay = [];
                                foreach($employeeRosters as $roster) {
                                    $rosterByDay[$roster->day] = $roster;
                                    if ($roster->start_time != '00:00:00') {
                                        $diff = (strtotime($roster->end_time) - strtotime($roster->start_time)) / 3600;
                                        $totalHours += ceil($diff);
                                    }
                                }
                            @endphp
                            <tr class="roster-row hover:bg-gray-50" 
                                data-row-index="{{ $loop->index }}"
                                data-employee="{{ strtolower($employee->firstname . ' ' . $employee->lastname) }}"
                                data-total="{{ $totalHours }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $employee->firstname }} {{ $employee->lastname }}
                                </td>
                                @foreach($days as $day)
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if(isset($rosterByDay[$day]) && $rosterByDay[$day]->start_time != '00:00:00')
                                            @php
                                                $r = $rosterByDay[$day];
                                                $diff = (strtotime($r->end_time) - strtotime($r->start_time)) / 3600;
                                                $color = $diff <= 6 ? 'green' : 'red';
                                            @endphp
                                            <div>{{ date('H:i', strtotime($r->start_time)) }} to {{ date('H:i', strtotime($r->end_time)) }}</div>
                                            <div class="text-{{ $color }}-600 text-xs">{{ ceil($diff) }}Hrs</div>
                                        @else
                                            Off
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-sm text-green-600 font-medium">{{ $totalHours }} Hours</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('storeowner.roster.edit', base64_encode($employee->employeeid)) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('storeowner.roster.destroy', base64_encode($employee->employeeid)) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this roster?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('storeowner.roster.view', base64_encode($employee->employeeid)) }}" class="text-green-600 hover:text-green-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noRosterRow">
                                <td colspan="10" class="px-4 py-6 text-center text-gray-500">No rosters found for this department</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Client-side Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $employeedata->count() }}</span> entries
                </div>
                <div id="paginationControls" class="flex items-center gap-2">
                    <!-- Pagination buttons will be generated by JavaScript -->
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
            const tbody = document.getElementById('rosterTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.roster-row'));
            filteredRows = [...allRows];
            
            const noRosterRow = document.getElementById('noRosterRow');
            if (noRosterRow && allRows.length > 0) {
                noRosterRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            const tbody = document.getElementById('rosterTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.roster-row'));
            
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
                    
                    if (sortColumn === 'total') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                        return sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
                    }
                    
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
                const noRosterRow = document.getElementById('noRosterRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noRosterRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noRosterRow') {
                        if (noRosterRow && noRosterRow.parentNode) {
                            tbody.insertBefore(row, noRosterRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.roster-row'));
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
                if (row.id !== 'noRosterRow') {
                    row.style.display = 'none';
                }
            });

            const noRosterRow = document.getElementById('noRosterRow');
            if (noRosterRow) {
                if (filteredRows.length === 0) {
                    noRosterRow.style.display = '';
                } else {
                    noRosterRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noRosterRow') {
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

        // Wait for jQuery to be available for form validation
        (function() {
            var retries = 0;
            var maxRetries = 50;
            
            function initRosterPage() {
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initRosterPage, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load');
                        return;
                    }
                }
                
                $(document).ready(function() {
                    // Sync start/end time dropdowns when "off" is selected
                    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    
                    days.forEach(function(day) {
                        $('#' + day + '_start').on('blur change', function() {
                            if ($(this).val() === 'off') {
                                $('#' + day + '_end').val('off');
                            }
                        });
                        
                        $('#' + day + '_end').on('blur change', function() {
                            if ($(this).val() === 'off') {
                                $('#' + day + '_start').val('off');
                            }
                        });
                    });
                });
            }
            
            initRosterPage();
        })();

        function checkWorkingHour(obj) {
            const empid = document.getElementById('employeeid_select').value;
            if (empid == '0') {
                alert('Please select Employee');
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                days.forEach(day => {
                    document.getElementById(day + '_start').value = 'off';
                    document.getElementById(day + '_end').value = 'off';
                });
                return false;
            }

            const rosterDayHrs = parseInt(document.getElementById('roster_day_hrs_' + empid).value);
            const rosterWeekHrs = parseInt(document.getElementById('roster_week_hrs_' + empid).value);

            const dayName = obj.id.replace('_start', '').replace('_end', '');
            const startSelect = document.getElementById(dayName + '_start');
            const endSelect = document.getElementById(dayName + '_end');

            if (startSelect.value !== 'off' && endSelect.value !== 'off') {
                if (endSelect.value <= startSelect.value) {
                    alert('Please select valid Start/End Time of ' + dayName);
                    endSelect.value = 'off';
                    return false;
                }

                // Calculate hours
                const start = new Date('2000-01-01 ' + startSelect.value);
                const end = new Date('2000-01-01 ' + endSelect.value);
                const diffHours = (end - start) / (1000 * 60 * 60);

                if (rosterDayHrs < diffHours) {
                    alert('Maximum roster day hours for this employee is over.');
                    obj.value = 'off';
                    return false;
                }
            }

            // Calculate total week hours
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            let totalWeekHours = 0;
            days.forEach(day => {
                const start = document.getElementById(day + '_start').value;
                const end = document.getElementById(day + '_end').value;
                if (start !== 'off' && end !== 'off') {
                    const startTime = new Date('2000-01-01 ' + start);
                    const endTime = new Date('2000-01-01 ' + end);
                    totalWeekHours += (endTime - startTime) / (1000 * 60 * 60);
                }
            });

            if (rosterWeekHrs < totalWeekHours) {
                alert('Maximum roster week hours for this employee is over.');
                obj.value = 'off';
                return false;
            }
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

