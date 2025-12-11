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
                        <a href="{{ route('storeowner.ordering.index') }}" class="ml-1 hover:text-gray-700">Ordering</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Settings</span>
                    </div>
                </li>
            </ol>
        </nav>
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

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Left Column: Purchasing Categories -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text-o mr-2"></i> Purchasing categories
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('storeowner.ordering.update-category') }}" method="POST" id="categoryForm">
                            @csrf
                            <div class="flex items-center mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Add New <span class="text-red-500">* &nbsp</span>
                                </label>
                                <input type="text" 
                                       name="category_name" 
                                       id="category_name" 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                            
                            <!-- Search and Per Page Controls -->
                            <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           id="categorySearchbox"
                                           placeholder="Search categories..." 
                                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-700">Show:</label>
                                    <select id="categoryPerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="text-sm text-gray-700">entries</span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="category" style="cursor: pointer;">
                                                Current categories <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="categoryTableBody">
                                        @if($purchaseCategories && count($purchaseCategories) > 0)
                                            @foreach($purchaseCategories as $category)
                                                <tr class="category-row hover:bg-gray-50" 
                                                    data-row-index="{{ $loop->index }}"
                                                    data-category="{{ strtolower($category->category_name) }}">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" title="{{ $category->category_name }}">
                                                        {{ $category->category_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('storeowner.ordering.edit-category', base64_encode($category->categoryid)) }}" 
                                                           class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" 
                                                           onclick="event.preventDefault(); openDeleteModal('category', '{{ base64_encode($category->categoryid) }}')"
                                                           class="text-red-600 hover:text-red-800" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="noCategoriesRow">
                                                <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    No categories found.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Client-side Pagination -->
                            <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing <span id="categoryShowingStart">1</span> to <span id="categoryShowingEnd">10</span> of <span id="categoryTotalEntries">{{ $purchaseCategories->count() }}</span> entries
                                </div>
                                <div id="categoryPaginationControls" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be generated by JavaScript -->
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end gap-4">
                                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                    Add
                                </button>
                                <a href="{{ route('storeowner.dashboard') }}" 
                                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Supplier Document Types -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800">
                            <i class="fas fa-file-text-o mr-2"></i> Supplier document types
                        </h3>
                    </div>
                    <div class="p-6">
                        <form action="{{ route('storeowner.ordering.update-doc-type') }}" method="POST" id="docTypeForm">
                            @csrf
                            <div class="mb-4 flex items-center">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Add New <span class="text-red-500">* &nbsp</span>
                                </label>
                                <input type="text" 
                                       name="docs_type_name" 
                                       id="docs_type_name" 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                            
                            <!-- Search and Per Page Controls -->
                            <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                                <div class="flex items-center gap-2">
                                    <input type="text" 
                                           id="docTypeSearchbox"
                                           placeholder="Search document types..." 
                                           class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-700">Show:</label>
                                    <select id="docTypePerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
                                        <option value="10" selected>10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                    <span class="text-sm text-gray-700">entries</span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="doctype" style="cursor: pointer;">
                                                Current types <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="docTypeTableBody">
                                        @if($supplierDocTypes && count($supplierDocTypes) > 0)
                                            @foreach($supplierDocTypes as $docType)
                                                <tr class="doctype-row hover:bg-gray-50" 
                                                    data-row-index="{{ $loop->index }}"
                                                    data-doctype="{{ strtolower($docType->docs_type_name) }}">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" title="{{ $docType->docs_type_name }}">
                                                        {{ $docType->docs_type_name }}
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                                        <a href="{{ route('storeowner.ordering.edit-doc-type', base64_encode($docType->docs_type_id)) }}" 
                                                           class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="#" 
                                                           onclick="event.preventDefault(); openDeleteModal('doctype', '{{ base64_encode($docType->docs_type_id) }}')"
                                                           class="text-red-600 hover:text-red-800" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr id="noDocTypesRow">
                                                <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-500">
                                                    No document types found.
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Client-side Pagination -->
                            <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    Showing <span id="docTypeShowingStart">1</span> to <span id="docTypeShowingEnd">10</span> of <span id="docTypeTotalEntries">{{ $supplierDocTypes->count() }}</span> entries
                                </div>
                                <div id="docTypePaginationControls" class="flex items-center gap-2">
                                    <!-- Pagination buttons will be generated by JavaScript -->
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-end gap-4">
                                <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                                    Add
                                </button>
                                <a href="{{ route('storeowner.dashboard') }}" 
                                   class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                    Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
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
                <div class="mb-4">
                    <p class="text-sm text-gray-700">Are you sure you want to delete?</p>
                </div>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                            Delete
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
        // Client-side pagination, search, and sorting for Categories
        let categoryCurrentPage = 1;
        let categoryPerPage = 10;
        let categoryAllRows = [];
        let categoryFilteredRows = [];
        let categorySortColumn = null;
        let categorySortDirection = 'asc';

        function initializeCategoryPagination() {
            const tbody = document.getElementById('categoryTableBody');
            categoryAllRows = Array.from(tbody.querySelectorAll('tr.category-row'));
            categoryFilteredRows = [...categoryAllRows];
            
            const noCategoriesRow = document.getElementById('noCategoriesRow');
            if (noCategoriesRow && categoryAllRows.length > 0) {
                noCategoriesRow.style.display = 'none';
            }
            
            categoryPerPage = parseInt(document.getElementById('categoryPerPageSelect').value);
            categoryCurrentPage = 1;
            updateCategoryDisplay();
        }

        function updateCategoryDisplay() {
            const tbody = document.getElementById('categoryTableBody');
            categoryAllRows = Array.from(tbody.querySelectorAll('tr.category-row'));
            
            const searchTerm = document.getElementById('categorySearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                categoryFilteredRows = categoryAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                categoryFilteredRows = [...categoryAllRows];
            }

            if (categorySortColumn) {
                categoryFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${categorySortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${categorySortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return categorySortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return categorySortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(categoryFilteredRows.length / categoryPerPage);
            const start = (categoryCurrentPage - 1) * categoryPerPage;
            const end = Math.min(start + categoryPerPage, categoryFilteredRows.length);

            if (categorySortColumn && categoryFilteredRows.length > 0) {
                const noCategoriesRow = document.getElementById('noCategoriesRow');
                categoryAllRows.forEach(row => {
                    if (row.id !== 'noCategoriesRow') {
                        row.remove();
                    }
                });
                
                categoryFilteredRows.forEach(row => {
                    if (row.id !== 'noCategoriesRow') {
                        if (noCategoriesRow && noCategoriesRow.parentNode) {
                            tbody.insertBefore(row, noCategoriesRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                categoryAllRows = Array.from(tbody.querySelectorAll('tr.category-row'));
                const sortedFilteredIndices = categoryFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                categoryAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                categoryFilteredRows = newFilteredRows;
            }

            categoryAllRows.forEach(row => {
                if (row.id !== 'noCategoriesRow') {
                    row.style.display = 'none';
                }
            });

            const noCategoriesRow = document.getElementById('noCategoriesRow');
            if (noCategoriesRow) {
                if (categoryFilteredRows.length === 0) {
                    noCategoriesRow.style.display = '';
                } else {
                    noCategoriesRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (categoryFilteredRows[i] && categoryFilteredRows[i].id !== 'noCategoriesRow') {
                    categoryFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('categoryShowingStart').textContent = categoryFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('categoryShowingEnd').textContent = end;
            document.getElementById('categoryTotalEntries').textContent = categoryFilteredRows.length;

            generateCategoryPaginationControls(totalPages);
        }

        function generateCategoryPaginationControls(totalPages) {
            const paginationDiv = document.getElementById('categoryPaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (categoryCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = categoryCurrentPage === 1;
            prevBtn.onclick = () => {
                if (categoryCurrentPage > 1) {
                    categoryCurrentPage--;
                    updateCategoryDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, categoryCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    categoryCurrentPage = 1;
                    updateCategoryDisplay();
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
                    (i === categoryCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    categoryCurrentPage = i;
                    updateCategoryDisplay();
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
                    categoryCurrentPage = totalPages;
                    updateCategoryDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (categoryCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = categoryCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (categoryCurrentPage < totalPages) {
                    categoryCurrentPage++;
                    updateCategoryDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortCategoryTable(column) {
            if (categorySortColumn === column) {
                categorySortDirection = categorySortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                categorySortColumn = column;
                categorySortDirection = 'asc';
            }

            // Reset all sort indicators in category table
            const categoryTable = document.querySelector('#categoryTableBody').closest('table');
            categoryTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            // Update the active column's sort indicator
            const clickedHeader = categoryTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = categorySortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            categoryCurrentPage = 1;
            updateCategoryDisplay();
        }

        // Client-side pagination, search, and sorting for Document Types
        let docTypeCurrentPage = 1;
        let docTypePerPage = 10;
        let docTypeAllRows = [];
        let docTypeFilteredRows = [];
        let docTypeSortColumn = null;
        let docTypeSortDirection = 'asc';

        function initializeDocTypePagination() {
            const tbody = document.getElementById('docTypeTableBody');
            docTypeAllRows = Array.from(tbody.querySelectorAll('tr.doctype-row'));
            docTypeFilteredRows = [...docTypeAllRows];
            
            const noDocTypesRow = document.getElementById('noDocTypesRow');
            if (noDocTypesRow && docTypeAllRows.length > 0) {
                noDocTypesRow.style.display = 'none';
            }
            
            docTypePerPage = parseInt(document.getElementById('docTypePerPageSelect').value);
            docTypeCurrentPage = 1;
            updateDocTypeDisplay();
        }

        function updateDocTypeDisplay() {
            const tbody = document.getElementById('docTypeTableBody');
            docTypeAllRows = Array.from(tbody.querySelectorAll('tr.doctype-row'));
            
            const searchTerm = document.getElementById('docTypeSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                docTypeFilteredRows = docTypeAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                docTypeFilteredRows = [...docTypeAllRows];
            }

            if (docTypeSortColumn) {
                docTypeFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${docTypeSortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${docTypeSortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return docTypeSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return docTypeSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(docTypeFilteredRows.length / docTypePerPage);
            const start = (docTypeCurrentPage - 1) * docTypePerPage;
            const end = Math.min(start + docTypePerPage, docTypeFilteredRows.length);

            if (docTypeSortColumn && docTypeFilteredRows.length > 0) {
                const noDocTypesRow = document.getElementById('noDocTypesRow');
                docTypeAllRows.forEach(row => {
                    if (row.id !== 'noDocTypesRow') {
                        row.remove();
                    }
                });
                
                docTypeFilteredRows.forEach(row => {
                    if (row.id !== 'noDocTypesRow') {
                        if (noDocTypesRow && noDocTypesRow.parentNode) {
                            tbody.insertBefore(row, noDocTypesRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                docTypeAllRows = Array.from(tbody.querySelectorAll('tr.doctype-row'));
                const sortedFilteredIndices = docTypeFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                docTypeAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                docTypeFilteredRows = newFilteredRows;
            }

            docTypeAllRows.forEach(row => {
                if (row.id !== 'noDocTypesRow') {
                    row.style.display = 'none';
                }
            });

            const noDocTypesRow = document.getElementById('noDocTypesRow');
            if (noDocTypesRow) {
                if (docTypeFilteredRows.length === 0) {
                    noDocTypesRow.style.display = '';
                } else {
                    noDocTypesRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (docTypeFilteredRows[i] && docTypeFilteredRows[i].id !== 'noDocTypesRow') {
                    docTypeFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('docTypeShowingStart').textContent = docTypeFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('docTypeShowingEnd').textContent = end;
            document.getElementById('docTypeTotalEntries').textContent = docTypeFilteredRows.length;

            generateDocTypePaginationControls(totalPages);
        }

        function generateDocTypePaginationControls(totalPages) {
            const paginationDiv = document.getElementById('docTypePaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (docTypeCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = docTypeCurrentPage === 1;
            prevBtn.onclick = () => {
                if (docTypeCurrentPage > 1) {
                    docTypeCurrentPage--;
                    updateDocTypeDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, docTypeCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    docTypeCurrentPage = 1;
                    updateDocTypeDisplay();
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
                    (i === docTypeCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    docTypeCurrentPage = i;
                    updateDocTypeDisplay();
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
                    docTypeCurrentPage = totalPages;
                    updateDocTypeDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (docTypeCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = docTypeCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (docTypeCurrentPage < totalPages) {
                    docTypeCurrentPage++;
                    updateDocTypeDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortDocTypeTable(column) {
            if (docTypeSortColumn === column) {
                docTypeSortDirection = docTypeSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                docTypeSortColumn = column;
                docTypeSortDirection = 'asc';
            }

            // Reset all sort indicators in docType table
            const docTypeTable = document.querySelector('#docTypeTableBody').closest('table');
            docTypeTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            // Update the active column's sort indicator
            const clickedHeader = docTypeTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = docTypeSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            docTypeCurrentPage = 1;
            updateDocTypeDisplay();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeCategoryPagination();
            initializeDocTypePagination();

            // Category search functionality
            document.getElementById('categorySearchbox')?.addEventListener('keyup', function() {
                categoryCurrentPage = 1;
                updateCategoryDisplay();
            });

            // Category per page change
            document.getElementById('categoryPerPageSelect')?.addEventListener('change', function() {
                categoryPerPage = parseInt(this.value);
                categoryCurrentPage = 1;
                updateCategoryDisplay();
            });

            // Category sort functionality
            const categoryTable = document.querySelector('#categoryTableBody').closest('table');
            if (categoryTable) {
                categoryTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortCategoryTable(column);
                        }
                    });
                });
            }

            // DocType search functionality
            document.getElementById('docTypeSearchbox')?.addEventListener('keyup', function() {
                docTypeCurrentPage = 1;
                updateDocTypeDisplay();
            });

            // DocType per page change
            document.getElementById('docTypePerPageSelect')?.addEventListener('change', function() {
                docTypePerPage = parseInt(this.value);
                docTypeCurrentPage = 1;
                updateDocTypeDisplay();
            });

            // DocType sort functionality
            const docTypeTable = document.querySelector('#docTypeTableBody').closest('table');
            if (docTypeTable) {
                docTypeTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortDocTypeTable(column);
                        }
                    });
                });
            }
        });

        function openDeleteModal(type, id) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            
            if (type === 'category') {
                form.action = '{{ route("storeowner.ordering.delete-category", ":id") }}'.replace(':id', id);
            } else if (type === 'doctype') {
                form.action = '{{ route("storeowner.ordering.delete-doc-type", ":id") }}'.replace(':id', id);
            }
            
            modal.classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-storeowner-app-layout>

