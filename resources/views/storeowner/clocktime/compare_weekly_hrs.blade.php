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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Hours</span>
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
                           class="px-4 py-2 block text-gray-800 border-b-2 border-gray-800 font-medium">
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
                           placeholder="Search hours..." 
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
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="hours-table">
                        <thead class="bg-gray-50">
                            <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="week" style="cursor: pointer;">
                                        Week <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="employee" style="cursor: pointer;">
                                        Employee Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="hours" style="cursor: pointer;">
                                        Hours <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="month" style="cursor: pointer;">
                                        Month <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="year" style="cursor: pointer;">
                                        Year <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                </th>
                                    <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="hoursTableBody">
                            @if($empPayrollHrs->count() > 0)
                                @foreach($empPayrollHrs as $payroll)
                                        @php
                                            $weekStartDate = \Carbon\Carbon::parse($payroll->week_start)->format('Y-m-d');
                                            $monthName = \Carbon\Carbon::parse($payroll->week_start)->format('F');
                                        @endphp
                                        <tr class="hours-row hover:bg-gray-50" 
                                            data-row-index="{{ $loop->index }}"
                                            data-week="{{ strtolower($payroll->weekno . ' ' . $weekStartDate . ' ' . $monthName . ' ' . $payroll->year) }}"
                                            data-employee="{{ strtolower(($payroll->firstname ?? 'N/A') . ' ' . ($payroll->lastname ?? '')) }}"
                                            data-hours="{{ (float)$payroll->hours_worked }}"
                                            data-month="{{ strtolower($monthName) }}"
                                            data-year="{{ $payroll->year }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.weekly-hrs-byweek', ['weekno' => base64_encode($payroll->weekno), 'year' => base64_encode($payroll->year)]) }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $weekStartDate }} - {{ $payroll->weekno }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.weekly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $payroll->firstname ?? 'N/A' }} {{ $payroll->lastname ?? '' }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ number_format((float)$payroll->hours_worked, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.monthly_hrs_allemployee') }}" class="text-blue-600 hover:text-blue-800">
                                                    {{ $monthName }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', ['employeeid' => base64_encode($payroll->employeeid)]) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $payroll->year }}
                                            </a>
                                        </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                <a href="{{ route('storeowner.clocktime.edit-employee-hours', base64_encode($payroll->payroll_id ?? 0)) }}" 
                                                   class="inline-block text-blue-600 hover:text-blue-800 mr-3" 
                                                   title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                                <a href="#" 
                                                   class="inline-block text-red-600 hover:text-red-800 delete-payroll-hour" 
                                                   title="Delete"
                                                   data-href="{{ route('storeowner.employeepayroll.delete-payroll-hour', base64_encode($payroll->payroll_id ?? 0)) }}">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                    <tr id="noHoursRow">
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
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

            <!-- Delete Confirmation Modal -->
            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="confirm-delete-modal">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <div class="mt-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Delete</h3>
                            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('confirm-delete-modal').classList.add('hidden')">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="mt-2 px-7 py-3">
                            <p class="text-sm text-gray-500">Are you sure you want to delete this payroll hours record?</p>
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <button type="button" 
                                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400"
                                    onclick="document.getElementById('confirm-delete-modal').classList.add('hidden')">
                                Cancel
                            </button>
                            <form id="delete-confirm-form" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
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
            const tbody = document.getElementById('hoursTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.hours-row'));
            filteredRows = [...allRows];
            
            const noHoursRow = document.getElementById('noHoursRow');
            if (noHoursRow && allRows.length > 0) {
                noHoursRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            const tbody = document.getElementById('hoursTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.hours-row'));
            
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
                    const aValue = a.getAttribute(`data-${sortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${sortColumn}`) || '';
                    
                    // Handle numeric sorting for 'hours' and 'year'
                    if (sortColumn === 'hours' || sortColumn === 'year' || sortColumn === 'week') {
                        const numA = parseFloat(aValue);
                        const numB = parseFloat(bValue);
                        if (numA < numB) return sortDirection === 'asc' ? -1 : 1;
                        if (numA > numB) return sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    } else {
                        // String comparison for other columns
                        if (aValue < bValue) return sortDirection === 'asc' ? -1 : 1;
                        if (aValue > bValue) return sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    }
                });
            }

            const totalPages = Math.ceil(filteredRows.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, filteredRows.length);

            if (sortColumn && filteredRows.length > 0) {
                const noHoursRow = document.getElementById('noHoursRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noHoursRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noHoursRow') {
                        if (noHoursRow && noHoursRow.parentNode) {
                            tbody.insertBefore(row, noHoursRow);
                } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.hours-row'));
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
                if (row.id !== 'noHoursRow') {
                    row.style.display = 'none';
                }
            });

            const noHoursRow = document.getElementById('noHoursRow');
            if (noHoursRow) {
                if (filteredRows.length === 0) {
                    noHoursRow.style.display = '';
                } else {
                    noHoursRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noHoursRow') {
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
            
            // Delete confirmation modal setup
            const confirmDeleteModal = document.getElementById('confirm-delete-modal');
            const deleteConfirmForm = document.getElementById('delete-confirm-form');

            document.getElementById('hoursTableBody').addEventListener('click', function(e) {
                const deleteButton = e.target.closest('.delete-payroll-hour');
                if (deleteButton) {
                    e.preventDefault();
                    const deleteUrl = deleteButton.dataset.href;
                    deleteConfirmForm.action = deleteUrl;
                    confirmDeleteModal.classList.remove('hidden');
                }
            });

            confirmDeleteModal.addEventListener('click', function(e) {
                if (e.target === confirmDeleteModal) {
                    confirmDeleteModal.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
</x-storeowner-app-layout>

