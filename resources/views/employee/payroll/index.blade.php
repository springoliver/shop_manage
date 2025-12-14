@section('page_header', 'My Payroll')
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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">My Payroll</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search and Per Page Controls -->
    <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <input type="text" 
                   id="searchbox"
                   placeholder="Search payrolls..." 
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

    <!-- Table Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table id="table-new" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="name" style="cursor: pointer;">
                                Employee Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="store_name" style="cursor: pointer;">
                                Store Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="payslipname" style="cursor: pointer;">
                                Payslip Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="weekid" style="cursor: pointer;">
                                Week <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="payrollTableBody">
                        @forelse ($myPayroll as $payroll)
                            <tr class="payroll-row hover:bg-gray-50" 
                                data-row-index="{{ $loop->index }}"
                                data-name="{{ strtolower($payroll['firstname'] . ' ' . $payroll['lastname']) }}"
                                data-store_name="{{ strtolower($payroll['store_name']) }}"
                                data-payslipname="{{ strtolower($payroll['payslipname']) }}"
                                data-weekid="{{ strtolower($payroll['weekid']) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($payroll['firstname']) }} {{ ucfirst($payroll['lastname']) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payroll['store_name'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payroll['payslipname'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $payroll['weeknumber'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <!-- View -->
                                        <a href="{{ route('employee.payroll.show', [
                                            'storeid' => base64_encode($payroll['storeid']),
                                            'employeeid' => base64_encode($payroll['employeeid']),
                                            'weekid' => base64_encode($payroll['weekid'])
                                        ]) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <!-- Download -->
                                        <a href="{{ route('employee.payroll.download-pdf', base64_encode($payroll['payslipid'])) }}" class="text-green-600 hover:text-green-900" title="Download">
                                            Download
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noPayrollRow">
                                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No payroll found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Client-side Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $myPayroll->count() }}</span> entries
                </div>
                <div id="paginationControls" class="flex items-center gap-2">
                    <!-- Pagination buttons will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="mt-4 text-sm text-gray-600">
        <span class="font-medium">Legend(s):</span>
        <span class="ml-4"><i class="fas fa-eye text-blue-600"></i> View</span>
    </div>

    @push('styles')
    <style>
        /* Table cell height and borders - matching My Stores structure */
        table th,
        table td {
            height: 50px;
            border-bottom: 1px solid #e5e7eb;
            padding: 12px 24px;
        }
        
        table tbody tr:last-child td {
            border-bottom: none;
        }
    </style>
    @endpush

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
            
            // Hide no payroll row if there are payrolls
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
                    const aValue = a.getAttribute(`data-${sortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${sortColumn}`) || '';
                    
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
            }

            // Hide all rows first
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            // Show only rows for current page
            for (let i = start; i < end; i++) {
                if (filteredRows[i]) {
                    filteredRows[i].style.display = '';
                }
            }

            // Show "no payroll" row if no filtered results
            const noPayrollRow = document.getElementById('noPayrollRow');
            if (filteredRows.length === 0 && noPayrollRow) {
                noPayrollRow.style.display = '';
            } else if (noPayrollRow) {
                noPayrollRow.style.display = 'none';
            }

            // Update pagination info
            document.getElementById('showingStart').textContent = filteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('showingEnd').textContent = Math.min(end, filteredRows.length);
            document.getElementById('totalEntries').textContent = filteredRows.length;

            // Generate pagination controls
            generatePaginationControls(totalPages);
        }

        function generatePaginationControls(totalPages) {
            const controlsDiv = document.getElementById('paginationControls');
            controlsDiv.innerHTML = '';

            if (totalPages <= 1) return;

            // Previous button
            const prevButton = document.createElement('button');
            prevButton.textContent = 'Previous';
            prevButton.className = 'px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
            prevButton.disabled = currentPage === 1;
            prevButton.onclick = () => {
                if (currentPage > 1) {
                    currentPage--;
                    updateDisplay();
                }
            };
            controlsDiv.appendChild(prevButton);

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                    const pageButton = document.createElement('button');
                    pageButton.textContent = i;
                    pageButton.className = `px-4 py-2 text-sm font-medium border rounded-md mx-1 ${
                        i === currentPage
                            ? 'bg-gray-800 text-white border-gray-800'
                            : 'text-gray-700 bg-white border-gray-300 hover:bg-gray-50'
                    }`;
                    pageButton.onclick = () => {
                        currentPage = i;
                        updateDisplay();
                    };
                    controlsDiv.appendChild(pageButton);
                } else if (i === currentPage - 3 || i === currentPage + 3) {
                    const ellipsis = document.createElement('span');
                    ellipsis.textContent = '...';
                    ellipsis.className = 'px-2 text-gray-700';
                    controlsDiv.appendChild(ellipsis);
                }
            }

            // Next button
            const nextButton = document.createElement('button');
            nextButton.textContent = 'Next';
            nextButton.className = 'px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed';
            nextButton.disabled = currentPage === totalPages;
            nextButton.onclick = () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    updateDisplay();
                }
            };
            controlsDiv.appendChild(nextButton);
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            // Update sort indicators
            document.querySelectorAll('.sort-indicator i').forEach(icon => {
                icon.className = 'fas fa-sort text-gray-400';
            });

            const clickedHeader = document.querySelector(`[data-sort="${column}"]`);
            if (clickedHeader) {
                const icon = clickedHeader.querySelector('.sort-indicator i');
                if (icon) {
                    icon.className = sortDirection === 'asc' 
                        ? 'fas fa-sort-up text-gray-600' 
                        : 'fas fa-sort-down text-gray-600';
                }
            }

            updateDisplay();
        }

        // Search input event
        document.getElementById('searchbox')?.addEventListener('input', () => {
            currentPage = 1;
            updateDisplay();
        });

        // Per page select event
        document.getElementById('perPageSelect')?.addEventListener('change', () => {
            currentPage = 1;
            updateDisplay();
        });

        // Sort click events
        document.querySelectorAll('.sortable').forEach(header => {
            header.addEventListener('click', () => {
                const column = header.getAttribute('data-sort');
                if (column) {
                    sortTable(column);
                }
            });
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', initializePagination);
    </script>
    @endpush
</x-employee-app-layout>

