@section('page_header', $pageTitle == 'Active Employees' ? 'Active Employees' : 'Ex Employees')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Employees</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Add Button -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex items-center space-x-3">
            <a href="{{ route('storeowner.employee.index', ['type' => $toggleButtonType ?? 'ex']) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                {{ $toggleButtonText ?? 'Ex Employees' }}
            </a>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('storeowner.employee.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                <i class="fas fa-plus mr-2"></i>
                Add
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <button type="button" class="absolute top-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none'">&times;</button>
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search and Per Page Controls -->
    <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
        <div class="flex items-center gap-2">
            <input type="text" 
                   id="searchbox"
                   placeholder="Search employees..." 
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
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="email" style="cursor: pointer;">
                                Email Id - Username <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="password" style="cursor: pointer;">
                                Password <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="login" style="cursor: pointer;">
                                Clock-in app Login <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="status" style="cursor: pointer;">
                                Status <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="display" style="cursor: pointer;">
                                Display Timesheet & Holidays <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="employeeTableBody">
                        @forelse ($employees as $employee)
                            <tr class="employee-row hover:bg-gray-50" 
                                data-row-index="{{ $loop->index }}"
                                data-name="{{ strtolower(ucfirst($employee->firstname) . ' ' . ucfirst($employee->lastname)) }}"
                                data-email="{{ strtolower($employee->emailid) }}"
                                data-password="{{ strtolower(base64_decode($employee->password)) }}"
                                data-login="{{ $employee->emplogin_code }}"
                                data-status="{{ strtolower($employee->status) }}"
                                data-display="{{ strtolower($employee->display_hrs_hols ?? '') }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ ucfirst($employee->firstname) }} {{ ucfirst($employee->lastname) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->emailid }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ base64_decode($employee->password) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->emplogin_code }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button type="button" onclick="openStatusModal('{{ base64_encode($employee->employeeid) }}', '{{ $employee->status }}')" 
                                        class="px-2 py-1 text-xs font-semibold rounded-full {{ $employee->status === 'Active' ? 'bg-green-100 text-green-800' : ($employee->status === 'Deactivate' ? 'bg-yellow-100 text-yellow-800' : ($employee->status === 'Suspended' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $employee->status }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $employee->display_hrs_hols }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <!-- View -->
                                        <a href="{{ route('storeowner.employee.show', base64_encode($employee->employeeid)) }}" class="text-gray-600 hover:text-gray-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <!-- Edit -->
                                        <a href="{{ route('storeowner.employee.edit', base64_encode($employee->employeeid)) }}" class="text-gray-600 hover:text-gray-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <!-- Delete -->
                                        <form action="{{ route('storeowner.employee.destroy', base64_encode($employee->employeeid)) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noEmployeesRow">
                                <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No employees found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Client-side Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $employees->count() }}</span> entries
                </div>
                <div id="paginationControls" class="flex items-center gap-2">
                    <!-- Pagination buttons will be generated by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Change Status</h3>
                    <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" method="POST">
                    @csrf
                    <input type="hidden" name="employeeid" id="modal_employeeid">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Active" class="mr-2">
                                <span>Active</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Deactivate" class="mr-2">
                                <span>Deactivate</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Suspended" class="mr-2">
                                <span>Suspended</span>
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            No
                        </button>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Yes
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
            const tbody = document.getElementById('employeeTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.employee-row'));
            filteredRows = [...allRows];
            
            // Hide no employees row if there are employees
            const noEmployeesRow = document.getElementById('noEmployeesRow');
            if (noEmployeesRow && allRows.length > 0) {
                noEmployeesRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
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
                    
                    // Handle numeric values for login code
                    if (sortColumn === 'login') {
                        const aNum = parseInt(aValue) || 0;
                        const bNum = parseInt(bValue) || 0;
                        return sortDirection === 'asc' ? aNum - bNum : bNum - aNum;
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

            // Hide all rows first
            allRows.forEach(row => {
                row.style.display = 'none';
            });

            // Show/hide no employees message
            const noEmployeesRow = document.getElementById('noEmployeesRow');
            if (noEmployeesRow) {
                if (filteredRows.length === 0) {
                    noEmployeesRow.style.display = '';
                } else {
                    noEmployeesRow.style.display = 'none';
                }
            }

            // Calculate pagination
            const totalPages = Math.ceil(filteredRows.length / perPage);
            const start = (currentPage - 1) * perPage;
            const end = Math.min(start + perPage, filteredRows.length);

            // Reorder rows in DOM if sorted (before showing/hiding)
            if (sortColumn && filteredRows.length > 0) {
                const tbody = document.getElementById('employeeTableBody');
                const noEmployeesRow = document.getElementById('noEmployeesRow');
                
                // Get all employee rows (excluding noEmployeesRow) from current DOM
                const existingRows = Array.from(tbody.querySelectorAll('tr.employee-row'));
                
                // Only reorder if we have rows to sort
                if (existingRows.length > 0) {
                    // Create a map of row data-index to row element for quick lookup
                    const rowMap = new Map();
                    filteredRows.forEach(row => {
                        const index = row.getAttribute('data-row-index');
                        if (index !== null) {
                            rowMap.set(index, row);
                        }
                    });
                    
                    // Remove all employee rows from DOM (they'll be re-added in sorted order)
                    existingRows.forEach(row => {
                        if (row.id !== 'noEmployeesRow') {
                            row.remove();
                        }
                    });
                    
                    // Insert sorted rows in correct order
                    filteredRows.forEach(row => {
                        if (row.id !== 'noEmployeesRow') {
                            if (noEmployeesRow && noEmployeesRow.parentNode) {
                                tbody.insertBefore(row, noEmployeesRow);
                            } else {
                                tbody.appendChild(row);
                            }
                        }
                    });
                }
            }

            // Hide all rows first, then show only current page rows
            filteredRows.forEach(row => {
                row.style.display = 'none';
            });

            // Show rows for current page
            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noEmployeesRow') {
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

        function openStatusModal(employeeid, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            form.action = '{{ route("storeowner.employee.change-status") }}';
            
            // Set employeeid
            document.getElementById('modal_employeeid').value = employeeid;
            
            // Set current status
            const radios = form.querySelectorAll('input[name="status"]');
            radios.forEach(radio => {
                radio.checked = radio.value === currentStatus;
            });
            
            modal.classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-storeowner-app-layout>

