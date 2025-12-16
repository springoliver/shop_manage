@section('page_header', 'Time Off Request')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Time Off Request</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <a href="{{ route('storeowner.holidayrequest.calenderview') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                Time Off Requests Calendar View
            </a>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('storeowner.holidayrequest.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                <i class="fas fa-plus mr-2"></i>
                Add Request
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
                   placeholder="Search holiday requests..." 
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
                <table class="min-w-full divide-y divide-gray-200" id="table-new">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="employee" style="cursor: pointer;">
                                Employee Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="from-date" style="cursor: pointer;">
                                From Date <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="to-date" style="cursor: pointer;">
                                To Date <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="subject" style="cursor: pointer;">
                                Subject <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="status" style="cursor: pointer;">
                                Status <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="holidayRequestTableBody">
                        @forelse($holidayRequests as $request)
                            <tr class="holiday-request-row hover:bg-gray-50" 
                                data-row-index="{{ $loop->index }}"
                                data-employee="{{ strtolower(ucfirst($request->employee->firstname ?? '') . ' ' . ucfirst($request->employee->lastname ?? '')) }}"
                                data-from-date="{{ $request->from_date->format('Y-m-d') }}"
                                data-to-date="{{ $request->to_date->format('Y-m-d') }}"
                                data-subject="{{ strtolower($request->subject) }}"
                                data-status="{{ strtolower($request->status) }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <a href="javascript:void(0);" onclick="viewModal('{{ $request->employeeid }}', '{{ $request->requestid }}')" class="text-blue-600 hover:text-blue-800 cursor-pointer">
                                        {{ ucfirst($request->employee->firstname ?? '') }} {{ ucfirst($request->employee->lastname ?? '') }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->from_date->format('F d Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->to_date->format('F d Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $request->subject }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($request->status == 'Pending')
                                        <button type="button" onclick="openStatusModal('{{ base64_encode($request->requestid) }}', 'Pending')" class="px-3 py-1 text-xs font-medium rounded-md bg-yellow-100 text-yellow-800 hover:bg-yellow-200">
                                            Pending
                                        </button>
                                    @elseif($request->status == 'Declined')
                                        <button type="button" onclick="openStatusModal('{{ base64_encode($request->requestid) }}', 'Declined')" class="px-3 py-1 text-xs font-medium rounded-md bg-red-100 text-red-800 hover:bg-red-200">
                                            Declined
                                        </button>
                                    @else
                                        <button type="button" onclick="openStatusModal('{{ base64_encode($request->requestid) }}', 'Approved')" class="px-3 py-1 text-xs font-medium rounded-md bg-green-100 text-green-800 hover:bg-green-200">
                                            Approved
                                        </button>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('storeowner.holidayrequest.show', base64_encode($request->requestid)) }}" class="text-green-600 hover:text-green-900" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('storeowner.holidayrequest.edit', base64_encode($request->requestid)) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" onclick="openDeleteModal('{{ base64_encode($request->requestid) }}')" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr id="noHolidayRequestsRow">
                                <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No holiday requests found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Client-side Pagination -->
            <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $holidayRequests->count() }}</span> entries
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
                    <input type="hidden" name="requestid" id="modal_requestid">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Pending" class="mr-2">
                                <span>Pending</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Approved" class="mr-2">
                                <span>Approved</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="status" value="Declined" class="mr-2">
                                <span>Declined</span>
                            </label>
                        </div>
                    </div>
                    <div id="reasonBox" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Reason <span class="text-red-500">*</span></label>
                        <textarea name="reasonbox" id="reasonbox" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" placeholder="Please enter reason for declining"></textarea>
                        <p class="mt-1 text-xs text-red-600 hidden" id="reasonError">Reason is required when declining a request.</p>
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

    <!-- Employee Request Modal -->
    <div id="requestModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Employee Requests</h3>
                    <button onclick="closeRequestModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div id="requestModalContent"></div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Delete</h3>
                    <button onclick="closeDeleteModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <p class="mb-4">Are you sure you want to delete this request?</p>
                <div class="flex items-center justify-end space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <form id="deleteForm" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
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
        let sortDirection = 'asc';

        function initializePagination() {
            const tbody = document.getElementById('holidayRequestTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.holiday-request-row'));
            filteredRows = [...allRows];
            
            const noHolidayRequestsRow = document.getElementById('noHolidayRequestsRow');
            if (noHolidayRequestsRow && allRows.length > 0) {
                noHolidayRequestsRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            const tbody = document.getElementById('holidayRequestTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.holiday-request-row'));
            
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
                    
                    // Handle date sorting for from-date and to-date
                    if (sortColumn === 'from-date' || sortColumn === 'to-date') {
                        const dateA = new Date(aValue);
                        const dateB = new Date(bValue);
                        if (dateA < dateB) return sortDirection === 'asc' ? -1 : 1;
                        if (dateA > dateB) return sortDirection === 'asc' ? 1 : -1;
                        return 0;
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
                const noHolidayRequestsRow = document.getElementById('noHolidayRequestsRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noHolidayRequestsRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noHolidayRequestsRow') {
                        if (noHolidayRequestsRow && noHolidayRequestsRow.parentNode) {
                            tbody.insertBefore(row, noHolidayRequestsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.holiday-request-row'));
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
                if (row.id !== 'noHolidayRequestsRow') {
                    row.style.display = 'none';
                }
            });

            const noHolidayRequestsRow = document.getElementById('noHolidayRequestsRow');
            if (noHolidayRequestsRow) {
                if (filteredRows.length === 0) {
                    noHolidayRequestsRow.style.display = '';
                } else {
                    noHolidayRequestsRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noHolidayRequestsRow') {
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

        // Status Modal
        function openStatusModal(requestid, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            const reasonBox = document.getElementById('reasonBox');
            const reasonInput = document.getElementById('reasonbox');
            
            form.action = '{{ route("storeowner.holidayrequest.change-status") }}';
            document.getElementById('modal_requestid').value = requestid;
            
            // Set current status
            const radios = form.querySelectorAll('input[name="status"]');
            radios.forEach(radio => {
                if (radio.value === currentStatus) {
                    radio.checked = true;
                }
                
                radio.addEventListener('change', function() {
                    if (this.value === 'Declined') {
                        reasonBox.classList.remove('hidden');
                        reasonInput.setAttribute('required', 'required');
                        document.getElementById('reasonError').classList.add('hidden');
                    } else {
                        reasonBox.classList.add('hidden');
                        reasonInput.removeAttribute('required');
                        reasonInput.value = '';
                        document.getElementById('reasonError').classList.add('hidden');
                    }
                });
            });
            
            // Show/hide reason box based on current status
            if (currentStatus === 'Declined') {
                reasonBox.classList.remove('hidden');
                reasonInput.setAttribute('required', 'required');
            } else {
                reasonBox.classList.add('hidden');
                reasonInput.removeAttribute('required');
            }
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const selectedStatus = form.querySelector('input[name="status"]:checked');
                if (selectedStatus && selectedStatus.value === 'Declined') {
                    if (!reasonInput.value.trim()) {
                        e.preventDefault();
                        document.getElementById('reasonError').classList.remove('hidden');
                        reasonInput.focus();
                        return false;
                    }
                }
            });
            
            modal.classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Request Modal
        function viewModal(employeeid, requestid) {
            const modal = document.getElementById('requestModal');
            const content = document.getElementById('requestModalContent');
            
            // Create form data
            const formData = new FormData();
            formData.append('employeeid', employeeid);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("storeowner.holidayrequest.view-request") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                content.innerHTML = html;
                modal.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function closeRequestModal() {
            document.getElementById('requestModal').classList.add('hidden');
        }

        // Delete Modal
        function openDeleteModal(requestid) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = '{{ route("storeowner.holidayrequest.destroy", ":id") }}'.replace(':id', requestid);
            modal.classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const statusModal = document.getElementById('statusModal');
            const requestModal = document.getElementById('requestModal');
            const deleteModal = document.getElementById('deleteModal');
            
            if (event.target == statusModal) {
                closeStatusModal();
            }
            if (event.target == requestModal) {
                closeRequestModal();
            }
            if (event.target == deleteModal) {
                closeDeleteModal();
            }
        }
    </script>
    @endpush
</x-storeowner-app-layout>

