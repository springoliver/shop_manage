@section('page_header', 'Add Bill')
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
                        <a href="{{ route('storeowner.ordering.index') }}" class="ml-1 hover:text-gray-700">Ordering</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Orders Reporting</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <div class="py-4">
        <div class="mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Navigation Tabs -->
            <div class="mb-4">
                <div class="flex space-x-2 border-b border-gray-200 mb-4">
                    <a href="{{ route('storeowner.ordering.tax_analysis') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Tax Analysis
                    </a>
                    <a href="{{ route('storeowner.ordering.add_invoice') }}" 
                       class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                        Add Bills
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Bill Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-file-text mr-2"></i> Add Bill
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('storeowner.ordering.new_bill') }}" method="POST" id="myform" name="myform">
                                @csrf
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery/Order Date-Month:</label>
                                    <input type="date" 
                                           name="delivery_date" 
                                           id="delivery_date"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                                    <select name="categoryid" id="categoryid" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($purchaseOrdersCategory as $category)
                                            <option value="{{ $category->categoryid }}">{{ $category->category_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title:</label>
                                        <input type="text" 
                                               name="products_bought" 
                                               id="products_bought"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice No:</label>
                                        <input type="text" 
                                               name="invoicenumber" 
                                               id="invoicenumber"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice exist:</label>
                                        <div class="mt-2">
                                            <label class="inline-flex items-center mr-4">
                                                <input type="radio" name="invoicestatus" value="Yes" checked class="form-radio">
                                                <span class="ml-2">Yes</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="invoicestatus" value="No" class="form-radio">
                                                <span class="ml-2">No</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Net Amount:</label>
                                        <input type="number" 
                                               step="0.01"
                                               name="total_amount" 
                                               id="total_amount"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax:</label>
                                        <input type="number" 
                                               step="0.01"
                                               name="total_tax" 
                                               id="total_tax"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                        <div class="result text-red-500 text-sm mt-1"></div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount:</label>
                                        <input type="number" 
                                               step="0.01"
                                               name="amount_inc_tax" 
                                               id="amount_inc_tax"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Save
                                    </button>
                                    <a href="{{ route('storeowner.ordering.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Calculator -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-calculator mr-2"></i> Calculator
                            </h3>
                        </div>
                        <div class="p-6">
                            <table class="w-full">
                                <tr>
                                    <td class="py-2">Amount</td>
                                    <td class="py-2">
                                        <input type="number" id="txtnum1" onchange="Subtract()" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2">23% - 1.23<br>9% - 1.09</td>
                                    <td class="py-2">
                                        <input type="number" step="0.01" id="txtnum2" onchange="Subtract()" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="py-2">Result</td>
                                    <td class="py-2">
                                        <input type="number" id="txtres" readonly
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-100">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bills Table -->
            <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6">
                    <!-- Search and Per Page Controls -->
                    <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   id="searchbox"
                                   placeholder="Search bills..." 
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
                        <table class="min-w-full divide-y divide-gray-200" id="table-report">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="type" style="cursor: pointer;">
                                        Type <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="category" style="cursor: pointer;">
                                        Category <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="supplier" style="cursor: pointer;">
                                        Supplier <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="desc" style="cursor: pointer;">
                                        Desc <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="date" style="cursor: pointer;">
                                        Date <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="month" style="cursor: pointer;">
                                        Month <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="tax" style="cursor: pointer;">
                                        Total Tax Amount <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="total" style="cursor: pointer;">
                                        Total Amount <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="inc-tax" style="cursor: pointer;">
                                        Amount Inc. Tax <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="invoice" style="cursor: pointer;">
                                        Invoice <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="invoice-no" style="cursor: pointer;">
                                        Invoice No: <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                    </th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="billsTableBody">
                                @forelse($reports as $report)
                                    <tr class="bill-row hover:bg-gray-50" 
                                        data-row-index="{{ $loop->index }}"
                                        data-type="{{ strtolower($report->purchase_orders_type) }}"
                                        data-category="{{ strtolower($report->category->category_name ?? 'purchasing') }}"
                                        data-supplier="{{ strtolower($report->supplier->supplier_name ?? 'n/a') }}"
                                        data-desc="{{ strtolower($report->products_bought ?? '') }}"
                                        data-date="{{ $report->delivery_date ? $report->delivery_date->format('Y-m-d') : '' }}"
                                        data-month="{{ $report->delivery_date ? strtolower($report->delivery_date->format('F - Y')) : '' }}"
                                        data-tax="{{ $report->total_tax ?? 0 }}"
                                        data-total="{{ $report->total_amount ?? 0 }}"
                                        data-inc-tax="{{ $report->amount_inc_tax ?? 0 }}"
                                        data-invoice="{{ strtolower($report->invoicestatus) }}"
                                        data-invoice-no="{{ strtolower($report->invoicenumber ?? '') }}">
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $report->purchase_orders_type }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $report->category->category_name ?? 'Purchasing' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $report->supplier->supplier_name ?? 'N/A' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $report->products_bought ?? '' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $report->delivery_date ? $report->delivery_date->format('Y-m-d') : '' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            {{ $report->delivery_date ? $report->delivery_date->format('F - Y') : '' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_tax ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_amount ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->amount_inc_tax ?? 0, 2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($report->invoicestatus == 'No')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Missing
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exist</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-900">{{ $report->invoicenumber ?? '' }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('storeowner.ordering.edit_bills', [
                                                    'purchase_orders_id' => base64_encode($report->purchase_orders_id),
                                                    'delivery_date' => base64_encode($report->delivery_date ? $report->delivery_date->format('Y-m-d') : '')
                                                ]) }}" 
                                                   class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   class="text-red-600 hover:text-red-800" 
                                                   title="Delete"
                                                   onclick="event.preventDefault(); openDeleteModal('{{ route('storeowner.ordering.delete-po', base64_encode($report->purchase_orders_id)) }}');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="noBillsRow">
                                        <td colspan="12" class="px-4 py-3 text-center text-sm text-gray-500">
                                            No bills found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Client-side Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                        <div class="text-sm text-gray-700">
                            Showing <span id="showingStart">1</span> to <span id="showingEnd">10</span> of <span id="totalEntries">{{ $reports->count() }}</span> entries
                        </div>
                        <div id="paginationControls" class="flex items-center gap-2">
                            <!-- Pagination buttons will be generated by JavaScript -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto w-96 shadow-lg rounded-lg bg-white overflow-hidden">
            <!-- Warning Header Section -->
            <div class="bg-red-50 rounded-t-lg px-6 py-4 text-center">
                <div class="mx-auto flex items-center justify-center mb-3">
                    <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Delete Purchase Order</h3>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 bg-white">
                <p class="text-sm text-gray-600 text-center">Are you sure you want to delete this purchase order? This action cannot be undone.</p>
            </div>
            
            <!-- Modal Footer -->
            <div class="px-6 py-4 bg-white border-t border-gray-200">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex flex-col space-y-2">
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors">
                            Delete
                        </button>
                        <button type="button" onclick="closeDeleteModal()" class="w-full px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Tax validation
        document.addEventListener('DOMContentLoaded', function() {
            const totalTaxInput = document.getElementById('total_tax');
            const totalAmountInput = document.getElementById('total_amount');
            const resultDiv = document.querySelector('.result');
            
            if (totalTaxInput && totalAmountInput) {
                totalTaxInput.addEventListener('keyup', function() {
                    var totalAmount = parseFloat(totalAmountInput.value) || 0;
                    var totalTax = parseFloat(this.value) || 0;
                    
                    if (totalAmount <= totalTax) {
                        if (resultDiv) {
                            resultDiv.innerHTML = 'Total amount should not be same or smaller than the Tax value!';
                        }
                    } else {
                        if (resultDiv) {
                            resultDiv.innerHTML = '';
                        }
                    }
                });
            }
        });

        // Calculator function
        function Subtract() {
            var num1 = parseFloat(document.getElementById("txtnum1").value) || 0;
            var num2 = parseFloat(document.getElementById("txtnum2").value) || 0;
            
            if (num2 > 0) {
                var res = num1 - (num1 / num2);
                document.getElementById("txtres").value = res.toFixed(2);
            }
        }

        // Delete modal functions
        function openDeleteModal(actionUrl) {
            const deleteForm = document.getElementById('deleteForm');
            const deleteModal = document.getElementById('deleteModal');
            if (deleteForm && deleteModal) {
                deleteForm.action = actionUrl;
                deleteModal.classList.remove('hidden');
            }
        }

        function closeDeleteModal() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                deleteModal.classList.add('hidden');
            }
        }

        // Initialize modal close handlers
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('deleteModal');
            if (deleteModal) {
                // Close modal when clicking outside
                deleteModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeDeleteModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                        closeDeleteModal();
                    }
                });
            }
        });

        // Client-side pagination, search, and sorting for Bills table
        let currentPage = 1;
        let perPage = 10;
        let allRows = [];
        let filteredRows = [];
        let sortColumn = null;
        let sortDirection = 'asc';

        function initializeBillsPagination() {
            const tbody = document.getElementById('billsTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.bill-row'));
            filteredRows = [...allRows];
            
            const noBillsRow = document.getElementById('noBillsRow');
            if (noBillsRow && allRows.length > 0) {
                noBillsRow.style.display = 'none';
            }
            
            perPage = parseInt(document.getElementById('perPageSelect').value);
            currentPage = 1;
            updateBillsDisplay();
        }

        function updateBillsDisplay() {
            const tbody = document.getElementById('billsTableBody');
            allRows = Array.from(tbody.querySelectorAll('tr.bill-row'));
            
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
                    
                    // Handle date sorting
                    if (sortColumn === 'date') {
                        const dateA = new Date(aValue);
                        const dateB = new Date(bValue);
                        if (dateA < dateB) return sortDirection === 'asc' ? -1 : 1;
                        if (dateA > dateB) return sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    }
                    
                    // Handle numeric sorting for tax, total, inc-tax
                    if (['tax', 'total', 'inc-tax'].includes(sortColumn)) {
                        const numA = parseFloat(aValue) || 0;
                        const numB = parseFloat(bValue) || 0;
                        if (numA < numB) return sortDirection === 'asc' ? -1 : 1;
                        if (numA > numB) return sortDirection === 'asc' ? 1 : -1;
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
                const noBillsRow = document.getElementById('noBillsRow');
                
                allRows.forEach(row => {
                    if (row.id !== 'noBillsRow') {
                        row.remove();
                    }
                });
                
                filteredRows.forEach(row => {
                    if (row.id !== 'noBillsRow') {
                        if (noBillsRow && noBillsRow.parentNode) {
                            tbody.insertBefore(row, noBillsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                allRows = Array.from(tbody.querySelectorAll('tr.bill-row'));
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
                if (row.id !== 'noBillsRow') {
                    row.style.display = 'none';
                }
            });

            const noBillsRow = document.getElementById('noBillsRow');
            if (noBillsRow) {
                if (filteredRows.length === 0) {
                    noBillsRow.style.display = '';
                } else {
                    noBillsRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (filteredRows[i] && filteredRows[i].id !== 'noBillsRow') {
                    filteredRows[i].style.display = '';
                }
            }

            document.getElementById('showingStart').textContent = filteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('showingEnd').textContent = end;
            document.getElementById('totalEntries').textContent = filteredRows.length;

            generateBillsPaginationControls(totalPages);
        }

        function generateBillsPaginationControls(totalPages) {
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
                    updateBillsDisplay();
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
                    updateBillsDisplay();
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
                    updateBillsDisplay();
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
                    updateBillsDisplay();
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
                    updateBillsDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortBillsTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            document.querySelectorAll('#table-report .sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = document.querySelector(`#table-report th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = sortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            currentPage = 1;
            updateBillsDisplay();
        }

        // Initialize bills pagination when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize bills pagination
            initializeBillsPagination();

            // Search functionality
            document.getElementById('searchbox')?.addEventListener('keyup', function() {
                currentPage = 1;
                updateBillsDisplay();
            });

            // Per page change
            document.getElementById('perPageSelect')?.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                updateBillsDisplay();
            });

            // Sort functionality
            document.querySelectorAll('#table-report .sortable').forEach(header => {
                header.addEventListener('click', function() {
                    const column = this.getAttribute('data-sort');
                    if (column) {
                        sortBillsTable(column);
                    }
                });
            });
        });
    </script>
    @endpush
</x-storeowner-app-layout>

