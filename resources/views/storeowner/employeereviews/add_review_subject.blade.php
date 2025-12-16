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
                        <a href="{{ route('storeowner.employeereviews.review-subjects') }}" class="ml-1 hover:text-gray-700">Review subjects</a>
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

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add/Edit Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Review Subjects</h2>
                
                <form action="{{ route('storeowner.employeereviews.update-review-subject') }}" method="POST" id="myform">
                    @csrf
                    @if(old('review_subjectid'))
                        <input type="hidden" name="review_subjectid" id="review_subjectid" value="{{ old('review_subjectid') }}">
                    @endif
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            User Group <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="usergroupid" id="usergroupid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select User Group</option>
                                @foreach($userGroups as $group)
                                    <option value="{{ $group->usergroupid }}" {{ old('usergroupid') == $group->usergroupid ? 'selected' : '' }}>
                                        {{ $group->groupname }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Review Subject Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="subject_name" id="subject_name" 
                                   value="{{ old('subject_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.employeereviews.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Existing Subjects Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <!-- Search and Per Page Controls -->
                    <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   id="searchbox"
                                   placeholder="Search review subjects..." 
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
                        <table class="min-w-full divide-y divide-gray-200" id="table-new">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="subject" style="cursor: pointer;">
                                        Review Subject <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="group" style="cursor: pointer;">
                                        User Group <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="reviewSubjectTableBody">
                                @if($subjects->count() > 0)
                                    @foreach($subjects as $subject)
                                        <tr class="review-subject-row hover:bg-gray-50" 
                                            data-row-index="{{ $loop->index }}"
                                            data-subject="{{ strtolower($subject->subject_name) }}"
                                            data-group="{{ strtolower($subject->groupname ?? '-') }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $subject->subject_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $subject->groupname ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('storeowner.employeereviews.edit-review-subject', base64_encode($subject->review_subjectid)) }}" 
                                                   class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this review subject?')) { document.getElementById('delete-form-{{ $subject->review_subjectid }}').submit(); }"
                                                   class="text-red-600 hover:text-red-800" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                                <form id="delete-form-{{ $subject->review_subjectid }}" 
                                                      action="{{ route('storeowner.employeereviews.destroy-review-subject', base64_encode($subject->review_subjectid)) }}" 
                                                      method="POST" 
                                                      style="display: none;">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr id="noReviewSubjectsRow">
                                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
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
                            Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $subjects->count() }}</span> entries
                        </div>
                        <div id="paginationControls" class="flex items-center gap-2">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Legend -->
            <div class="mt-4 text-sm text-gray-600">
                <strong>Legend(s):</strong>
                <i class="fas fa-edit ml-2"></i> Edit
                <i class="fas fa-trash ml-2"></i> Delete
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
            const tbody = document.getElementById('reviewSubjectTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.review-subject-row'));
            filteredRows = [...allRows];
            
            const noReviewSubjectsRow = document.getElementById('noReviewSubjectsRow');
            if (noReviewSubjectsRow && allRows.length > 0) {
                noReviewSubjectsRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            const tbody = document.getElementById('reviewSubjectTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.review-subject-row'));
            
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
                    
                    // String comparison for all columns
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
                const noReviewSubjectsRow = document.getElementById('noReviewSubjectsRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noReviewSubjectsRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noReviewSubjectsRow') {
                        if (noReviewSubjectsRow && noReviewSubjectsRow.parentNode) {
                            tbody.insertBefore(row, noReviewSubjectsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.review-subject-row'));
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
                if (row.id !== 'noReviewSubjectsRow') {
                    row.style.display = 'none';
                }
            });

            const noReviewSubjectsRow = document.getElementById('noReviewSubjectsRow');
            if (noReviewSubjectsRow) {
                if (filteredRows.length === 0) {
                    noReviewSubjectsRow.style.display = '';
                } else {
                    noReviewSubjectsRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noReviewSubjectsRow') {
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

