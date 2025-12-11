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
                        <a href="{{ route('storeowner.products.index') }}" class="ml-1 hover:text-gray-700">Products</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Supplier Products</span>
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
            <!-- Header with Supplier Name and Add Button -->
            <div class="flex justify-between items-center mb-2">
                <h1 class="text-2xl font-semibold text-gray-800">{{ $supplier->supplier_name }}</h1>
                <a href="{{ route('storeowner.products.add') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                    <i class="fas fa-plus mr-2"></i>
                    Add Products
                </a>
            </div>

            <!-- Search and Per Page Controls -->
            <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <input type="text" 
                           id="searchbox"
                           placeholder="Search products..." 
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
                                        Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="measure" style="cursor: pointer;">
                                        Measure <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="supplier" style="cursor: pointer;">
                                        Supplier <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="price" style="cursor: pointer;">
                                        Price <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="notes" style="cursor: pointer;">
                                        Notes <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="tax" style="cursor: pointer;">
                                        Tax Band <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="updated" style="cursor: pointer;">
                                        Last Updated <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="status" style="cursor: pointer;">
                                        Status <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="productTableBody">
                                @forelse ($storeProducts as $product)
                                    <tr class="product-row hover:bg-gray-50" 
                                        data-row-index="{{ $loop->index }}"
                                        data-name="{{ strtolower($product->product_name ?? '') }}"
                                        data-measure="{{ strtolower($product->purchasemeasure ?? '') }}"
                                        data-supplier="{{ strtolower($product->supplier_name ?? '') }}"
                                        data-price="{{ str_replace(['€', '£', '$', ','], '', $product->product_price ?? '0') }}"
                                        data-notes="{{ strtolower($product->product_notes ?? '') }}"
                                        data-tax="{{ strtolower($product->tax_name ?? '') }}"
                                        data-updated="{{ $product->editdate ? \Carbon\Carbon::parse($product->editdate)->format('Y-m-d') : '' }}"
                                        data-status="{{ strtolower($product->product_status ?? '') }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900" title="{{ $product->product_name }}">
                                                {{ $product->product_name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" title="{{ $product->purchasemeasure ?? '-' }}">
                                                {{ $product->purchasemeasure ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $product->supplier_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            <a href="#" 
                                               onclick="event.preventDefault(); openPriceModal('{{ base64_encode($product->productid) }}', '{{ $product->product_price }}')"
                                               class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                {{ $product->product_price }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" title="{{ $product->product_notes ?? '-' }}">
                                                {{ Str::limit($product->product_notes ?? '-', 30) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900" title="{{ $product->tax_name ?? '-' }}">
                                                {{ $product->tax_name ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $product->editdate ? \Carbon\Carbon::parse($product->editdate)->format('Y-m-d') : '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                            @if($product->product_status == 'Enable')
                                                <a href="#" 
                                                   onclick="event.preventDefault(); openStatusModal('{{ base64_encode($product->productid) }}', '{{ $product->product_status }}')"
                                                   class="px-3 py-1 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm">
                                                    Enable
                                                </a>
                                            @else
                                                <a href="#" 
                                                   onclick="event.preventDefault(); openStatusModal('{{ base64_encode($product->productid) }}', '{{ $product->product_status }}')"
                                                   class="px-3 py-1 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                                                    Disable
                                                </a>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-3">
                                                <!-- Edit -->
                                                <a href="{{ route('storeowner.products.edit', base64_encode($product->productid)) }}" 
                                                   class="text-gray-600 hover:text-gray-900" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <!-- Delete -->
                                                <a href="#" 
                                                   onclick="event.preventDefault(); openDeleteModal('{{ base64_encode($product->productid) }}')"
                                                   class="text-red-600 hover:text-red-900" 
                                                   title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noProductsRow">
                                        <td colspan="9" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No products found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Client-side Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $storeProducts->count() }}</span> entries
                        </div>
                        <div id="paginationControls" class="flex items-center gap-2">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </div>
                    </div>
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
                <form id="statusForm" method="POST" action="{{ route('storeowner.products.change-status') }}">
                    @csrf
                    <input type="hidden" name="productid" id="modal_productid">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="product_status" value="Enable" class="mr-2">
                                <span>Enable</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="product_status" value="Disable" class="mr-2">
                                <span>Disable</span>
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

    <!-- Price Change Modal -->
    <div id="priceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Change Price</h3>
                    <button onclick="closePriceModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="priceForm" method="POST" action="{{ route('storeowner.products.change-price') }}">
                    @csrf
                    <input type="hidden" name="productid" id="modal_price_productid">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Price</label>
                        <input type="text" name="product_price" id="modal_product_price" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                               required>
                    </div>
                    <div class="flex items-center justify-end space-x-3">
                        <button type="button" onclick="closePriceModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
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
                    <p class="text-sm text-gray-700">Are you sure you want to delete this Product?</p>
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
        // Client-side pagination, search, and sorting
        let currentPage = 1;
        let perPage = 10;
        let allRows = [];
        let filteredRows = [];
        let sortColumn = null;
        let sortDirection = 'asc'; // 'asc' or 'desc'

        function initializePagination() {
            const tbody = document.getElementById('productTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.product-row'));
            filteredRows = [...allRows];
            
            // Hide no products row if there are products
            const noProductsRow = document.getElementById('noProductsRow');
            if (noProductsRow && allRows.length > 0) {
                noProductsRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateDisplay();
        }

        function updateDisplay() {
            // Always refresh allRows from DOM to ensure valid references
            const tbody = document.getElementById('productTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.product-row'));
            
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
                    
                    // Handle numeric sorting for price
                    if (sortColumn === 'price') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                        return sortDirection === 'asc' ? aValue - bValue : bValue - aValue;
                    }
                    
                    // Handle date sorting for updated
                    if (sortColumn === 'updated') {
                        if (!aValue || aValue === '-') aValue = '0000-00-00';
                        if (!bValue || bValue === '-') bValue = '0000-00-00';
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
                const noProductsRow = document.getElementById('noProductsRow');
                
                // Remove all product rows from DOM (they'll be re-added in sorted order)
                allRows.forEach(row => {
                    if (row.id !== 'noProductsRow') {
                        row.remove();
                    }
                });
                
                // Insert sorted rows in correct order
                filteredRows.forEach(row => {
                    if (row.id !== 'noProductsRow') {
                        if (noProductsRow && noProductsRow.parentNode) {
                            tbody.insertBefore(row, noProductsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                // Update allRows and filteredRows after DOM reordering
                allRows = Array.from(tbody.querySelectorAll('tr.product-row'));
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
                if (row.id !== 'noProductsRow') {
                    row.style.display = 'none';
                }
            });

            // Show/hide no products message
            const noProductsRow = document.getElementById('noProductsRow');
            if (noProductsRow) {
                if (filteredRows.length === 0) {
                    noProductsRow.style.display = '';
                } else {
                    noProductsRow.style.display = 'none';
                }
            }

            // Show rows for current page
            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noProductsRow') {
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

        function openStatusModal(productid, currentStatus) {
            const modal = document.getElementById('statusModal');
            const form = document.getElementById('statusForm');
            document.getElementById('modal_productid').value = productid;
            
            // Set current status
            const radios = form.querySelectorAll('input[name="product_status"]');
            radios.forEach(radio => {
                radio.checked = radio.value === currentStatus;
            });
            
            modal.classList.remove('hidden');
        }
        
        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }
        
        function openPriceModal(productid, currentPrice) {
            const modal = document.getElementById('priceModal');
            const form = document.getElementById('priceForm');
            document.getElementById('modal_price_productid').value = productid;
            document.getElementById('modal_product_price').value = currentPrice;
            modal.classList.remove('hidden');
        }
        
        function closePriceModal() {
            document.getElementById('priceModal').classList.add('hidden');
        }
        
        function openDeleteModal(productid) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = '{{ route("storeowner.products.destroy", ":id") }}'.replace(':id', productid);
            modal.classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-storeowner-app-layout>

