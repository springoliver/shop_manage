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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employee Payroll</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Main Tabs -->
    <div class="mb-6 flex space-x-2">
        <a href="{{ route('storeowner.employeepayroll.employee-settings') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-t-md hover:bg-gray-700">
            Employee Settings
        </a>
        <a href="{{ route('storeowner.employeepayroll.index') }}" 
           class="px-4 py-2 bg-gray-600 text-white rounded-t-md hover:bg-gray-700">
            Employee Payslips
        </a>
        <a href="{{ route('storeowner.employeepayroll.process-payroll') }}" 
           class="px-4 py-2 bg-gray-800 text-white rounded-t-md">
            Process Payroll
        </a>
    </div>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex gap-6">
                <!-- Left Panel - Employee List -->
                <div class="w-1/4">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-4 py-3 border-b border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-800">Employee Name</h4>
                        </div>
                        <div class="p-4">
                            <ul class="space-y-2">
                                @foreach($employees as $employee)
                                    <li>
                                        <div class="text-sm cursor-pointer"> {{ $employee->firstname }} {{ $employee->lastname }} </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Right Panel - Payroll Hours Table -->
                <div class="flex-1 min-w-0">
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">Employee Payroll</h3>
                        </div>
                        
                        <!-- Search and Per Page Controls -->
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="searchbox"
                                       placeholder="Search payroll hours..." 
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
                        
                        <div class="overflow-x-auto">
                            <table class="w-full divide-y divide-gray-200" id="payroll-table">
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
                                <tbody class="bg-white divide-y divide-gray-200" id="payrollTableBody">
                                    @if($payrollHrs->count() > 0)
                                        @foreach($payrollHrs as $hrs)
                                            <tr class="payroll-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-week="{{ strtolower($hrs->weekno . ' ' . $hrs->week_start . ' ' . \Carbon\Carbon::parse($hrs->week_start)->format('F') . ' ' . $hrs->year) }}"
                                                data-employee="{{ strtolower(($hrs->firstname ?? '') . ' ' . ($hrs->lastname ?? '')) }}"
                                                data-hours="{{ $hrs->hours_worked ?? 0 }}"
                                                data-month="{{ strtolower(\Carbon\Carbon::parse($hrs->week_start)->format('F')) }}"
                                                data-year="{{ $hrs->year }}">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <a href="{{ route('storeowner.clocktime.weekly-hrs-byweek', [base64_encode($hrs->weekno), base64_encode($hrs->year)]) }}" 
                                                       class="text-blue-600 hover:text-blue-800 cursor-pointer" 
                                                       title="Week">
                                                        {{ $hrs->weekno }} - {{ $hrs->week_start }} - {{ \Carbon\Carbon::parse($hrs->week_start)->format('F') }} - {{ $hrs->year }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    @if($hrs->employeeid && $hrs->firstname)
                                                        <a href="{{ route('storeowner.clocktime.weekly-hrs-byemployee', base64_encode($hrs->employeeid)) }}" 
                                                           class="text-blue-600 hover:text-blue-800 cursor-pointer" 
                                                           title="Employee">
                                                            {{ ucfirst($hrs->firstname ?? '') }} {{ ucfirst($hrs->lastname ?? '') }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $hrs->hours_worked ?? 0 }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <a href="{{ route('storeowner.clocktime.monthly_hrs_allemployee') }}" 
                                                       class="text-blue-600 hover:text-blue-800 cursor-pointer" 
                                                       title="Month-Year">
                                                        {{ \Carbon\Carbon::parse($hrs->week_start)->format('F') }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <a href="{{ route('storeowner.clocktime.yearly-hrs-byemployee', base64_encode($hrs->employeeid ?? 0)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 cursor-pointer" 
                                                       title="Year">
                                                        {{ $hrs->year }}
                                                    </a>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                                    <a href="{{ route('storeowner.clocktime.edit-employee-hours', base64_encode($hrs->payroll_id ?? 0)) }}" 
                                                       class="inline-block text-blue-600 hover:text-blue-800 mr-3 cursor-pointer" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       class="inline-block text-red-600 hover:text-red-800 delete-payroll-hour cursor-pointer" 
                                                       title="Delete"
                                                       data-href="{{ route('storeowner.employeepayroll.delete-payroll-hour', base64_encode($hrs->payroll_id)) }}">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noPayrollRow">
                                            <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No records found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Client-side Pagination -->
                        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $payrollHrs->count() }}</span> entries
                            </div>
                            <div id="paginationControls" class="flex items-center gap-2">
                                <!-- Pagination buttons will be generated by JavaScript -->
                            </div>
                        </div>
                    </div>
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

    @push('scripts')
    <script>
        // Client-side pagination, search, and sorting
        let currentPage = 1;
        let perPage = 10;
        let allRows = [];
        let filteredRows = [];
        let sortColumn = null;
        let sortDirection = 'asc'; // 'asc' or 'desc'

        function initializePagination() {
            const tbody = document.getElementById('payrollTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.payroll-row'));
            filteredRows = [...allRows];
            
            // Hide no payroll row if there are records
            const noPayrollRow = document.getElementById('noPayrollRow');
            if (noPayrollRow && allRows.length > 0) {
                noPayrollRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            // Always refresh allRows from DOM to ensure valid references
            const tbody = document.getElementById('payrollTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.payroll-row'));
            
            // Filter rows based on search
            const searchTerm = document.getElementById('searchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                filteredRows = allRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                filteredRows = [...allRows];
            }

            // Sort rows if a sort column is selected
            if (sortColumn) {
                filteredRows.sort((a, b) => {
                    let aValue = a.getAttribute(`data-${sortColumn}`) || '';
                    let bValue = b.getAttribute(`data-${sortColumn}`) || '';
                    
                    // For numeric columns (hours, year), convert to numbers
                    if (sortColumn === 'hours' || sortColumn === 'year') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                        return sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
                    }
                    
                    // String comparison
                    if (aValue < bValue) {
                        return sortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return sortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            // Calculate pagination
            const totalPages = Math.ceil(filteredRows.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, filteredRows.length);

            // Reorder rows in DOM if sorted
            if (sortColumn && filteredRows.length > 0) {
                const noPayrollRow = document.getElementById('noPayrollRow');
                
                // Remove all payroll rows from DOM (they'll be re-added in sorted order)
                allRows.forEach(row => {
                    if (row.id !== 'noPayrollRow') {
                        row.remove();
                    }
                });
                
                // Insert sorted rows in correct order
                filteredRows.forEach(row => {
                    if (row.id !== 'noPayrollRow') {
                        if (noPayrollRow && noPayrollRow.parentNode) {
                            tbody.insertBefore(row, noPayrollRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                // Update allRows and filteredRows after DOM reordering
                allRows = Array.from(tbody.querySelectorAll('tr.payroll-row'));
                // Rebuild filteredRows from allRows in sorted order
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

            // Hide all rows first
            allRows.forEach(row => {
                if (row.id !== 'noPayrollRow') {
                    row.style.display = 'none';
                }
            });

            // Show/hide no payroll message
            const noPayrollRow = document.getElementById('noPayrollRow');
            if (noPayrollRow) {
                if (filteredRows.length === 0) {
                    noPayrollRow.style.display = '';
                } else {
                    noPayrollRow.style.display = 'none';
                }
            }

            // Show rows for current page
            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noPayrollRow') {
                    filteredRows[i].style.display = '';
                }
            }

            // Update pagination info
            document.getElementById('showingStart').textContent = filteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('totalEntries').textContent = filteredRows.length;

            // Generate pagination controls
            generatePaginationControls(totalPages);
        }

        function generatePaginationControls(totalPages) {
            const paginationDiv = document.getElementById('paginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            // Previous button
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

            // Page numbers
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

            // Next button
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
            // If clicking the same column, toggle direction
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            // Update sort indicators - reset all to default sort icon
            document.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            // Update the active column's sort indicator
            const clickedHeader = document.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = sortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            currentPage = 1; // Reset to first page when sorting
            updateDisplay();
        }

        // Wait for jQuery to be available for delete modal
        (function() {
            var retries = 0;
            var maxRetries = 50;
            
            function initProcessPayroll() {
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initProcessPayroll, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load');
                        return;
                    }
                }
                
                $(document).ready(function() {
                    // Delete confirmation modal
                    $('.delete-payroll-hour').on('click', function(e) {
                        e.preventDefault();
                        const deleteUrl = $(this).data('href');
                        $('#delete-confirm-form').attr('action', deleteUrl);
                        $('#confirm-delete-modal').removeClass('hidden');
                    });
                    
                    // Close modal when clicking outside
                    $('#confirm-delete-modal').on('click', function(e) {
                        if ($(e.target).is('#confirm-delete-modal')) {
                            $(this).addClass('hidden');
                        }
                    });
                });
            }
            
            initProcessPayroll();
        })();

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializePagination();

            // Search functionality
            document.getElementById('searchbox')?.addEventListener('keyup', function() {
                currentPage = 1; // Reset to first page on search
                updateDisplay();
            });

            // Per page change
            document.getElementById('perPageSelect')?.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                updateDisplay();
            });

            // Sort functionality - attach click handlers to sortable headers
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

