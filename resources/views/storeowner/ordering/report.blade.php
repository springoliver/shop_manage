@section('page_header', 'Purchase orders & Reports')
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

            <form action="{{ route('storeowner.ordering.report') }}" method="POST" id="myform" name="myform">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From:</label>
                        <input type="date" 
                               name="date_from" 
                               value="{{ old('date_from', request('date_from')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To:</label>
                        <input type="date" 
                               name="date_to" 
                               value="{{ old('date_to', request('date_to')) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier:</label>
                        <select name="supplierid" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option value="">--All Suppliers--</option>
                            @foreach($storeSuppliers as $supplier)
                                <option value="{{ $supplier->supplierid }}" {{ old('supplierid', request('supplierid')) == $supplier->supplierid ? 'selected' : '' }}>
                                    {{ $supplier->supplier_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">PO Type:</label>
                        <select name="purchase_orders_type" id="purchase_orders_type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                            <option value="">--Purchase order Type--</option>
                            <option value="">All</option>
                            <option value="Purchase order" {{ old('purchase_orders_type', request('purchase_orders_type')) == 'Purchase order' ? 'selected' : '' }}>Purchase Order</option>
                            <option value="Manual entry" {{ old('purchase_orders_type', request('purchase_orders_type')) == 'Manual entry' ? 'selected' : '' }}>Manual Entries</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
                        <button type="submit" name="submit" value="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Search
                        </button>
                    </div>
                </div>
            </form>

            <!-- Navigation Tabs -->
            <div class="mb-4">
                <ul class="flex space-x-2 border-b border-gray-200">
                    <li>
                        <a href="{{ route('storeowner.ordering.report') }}" 
                           class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                            Purchase orders
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.product_report') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Purchased Products
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.missing_delivery_dockets') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Delivery Dockets
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.credit_notes') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Credit Notes
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Chart View Navigation Tabs -->
            <div class="mb-4">
                <ul class="flex space-x-2 border-b border-gray-200">
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_yearly') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Yearly Orders Chart View
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_monthly') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Monthly Orders Chart View
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('storeowner.ordering.po_chart_weekly') }}" 
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                            Weekly Orders Chart View
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-text mr-2"></i> Orders Reporting
                    </h3>
                    <!-- Export Buttons -->
                    <div class="flex gap-2">
                        <button onclick="exportToCopy()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-100 bg-white text-gray-700">
                            Copy
                        </button>
                        <button onclick="exportToCSV()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-100 bg-white text-gray-700">
                            CSV
                        </button>
                        <button onclick="exportToPDF()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-100 bg-white text-gray-700">
                            PDF
                        </button>
                        <button onclick="exportToPrint()" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-100 bg-white text-gray-700">
                            Print
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-report">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desc</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No:</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. of Products</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tax Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Inc. Tax</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Docket</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $report->category->category_name ?? 'Purchasing' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->purchase_orders_type }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->products_bought ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->delivery_date ? $report->delivery_date->format('Y-m-d') : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->invoicenumber ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <a href="#" 
                                           class="text-blue-600 hover:underline">
                                            {{ $report->supplier->supplier_name ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($report->delivery_date)
                                            {{ $report->delivery_date->format('F - Y') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->total_products ?? 0 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_amount ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->amount_inc_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($report->deliverydocketstatus == 'No')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <a href="{{ route('storeowner.ordering.missing_delivery_dockets') }}">Missing</a>
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exist</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($report->invoicestatus == 'No')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <a href="#" class="text-white">Missing</a>
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exist</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($report->creditnote == 'Yes')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <a href="#" class="text-white">Credit Note</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="flex space-x-2">
                                            @if(($report->total_products ?? 0) == 0)
                                                <a href="#" 
                                                   class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @else
                                                <a href="#" 
                                                   class="text-blue-600 hover:text-blue-800" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            <a href="#" 
                                               onclick="event.preventDefault(); openDeleteModal('{{ base64_encode($report->purchase_orders_id) }}')"
                                               class="text-red-600 hover:text-red-800" 
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="px-4 py-3 text-center text-sm text-gray-500">
                                        No orders found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
                <div class="mt-2 px-2 py-3">
                    <p class="text-sm text-gray-500">Are you Sure you want to delete this Invoice?</p>
                </div>
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
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
        /* Print Styles */
        @media print {
            body * {
                visibility: hidden;
            }
            .bg-white.shadow.overflow-hidden,
            .bg-white.shadow.overflow-hidden * {
                visibility: visible;
            }
            .bg-white.shadow.overflow-hidden {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            button, .border-b, nav, .mb-6, .mb-4, .py-4 {
                display: none !important;
            }
            table {
                border-collapse: collapse;
                width: 100%;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
        }
        
        /* Table cell height and borders */
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
        // Export Functions - No CDN dependencies, Pure Vanilla JavaScript
        function getTableData() {
            const table = document.getElementById('table-report');
            const rows = [];
            
            // Get headers (skip Action column)
            const headerRow = table.querySelectorAll('thead th');
            const headers = [];
            headerRow.forEach((th, index) => {
                const text = th.textContent.trim();
                // Skip the last column if it's Action
                if (index < headerRow.length - 1) {
                    headers.push(text);
                }
            });
            rows.push(headers);
            
            // Get all visible data rows
            const dataRows = table.querySelectorAll('tbody tr');
            dataRows.forEach(row => {
                // Skip if row is hidden
                if (row.style.display === 'none') {
                    return;
                }
                
                const cells = row.querySelectorAll('td');
                const rowData = [];
                // Get all cells except the last one (Action column)
                for (let i = 0; i < cells.length - 1; i++) {
                    const cell = cells[i];
                    let text = cell.textContent.trim();
                    // Clean up text - remove extra spaces and newlines
                    text = text.replace(/\s+/g, ' ').trim();
                    rowData.push(text);
                }
                if (rowData.length > 0) {
                    rows.push(rowData);
                }
            });
            
            return rows;
        }

        function exportToCopy() {
            const rows = getTableData();
            const text = rows.map(row => row.join('\t')).join('\n');
            
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    alert('Table data copied to clipboard!');
                }).catch(() => {
                    copyToClipboardFallback(text);
                });
            } else {
                copyToClipboardFallback(text);
            }
        }

        function copyToClipboardFallback(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.left = '-999999px';
            textarea.style.top = '-999999px';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('Table data copied to clipboard!');
            } catch (err) {
                alert('Failed to copy data. Please try again.');
            }
            document.body.removeChild(textarea);
        }

        function exportToCSV() {
            const rows = getTableData();
            const csvContent = rows.map(row => {
                return row.map(cell => {
                    const cellText = String(cell || '').replace(/\n/g, ' ').replace(/\r/g, '');
                    if (cellText.includes(',') || cellText.includes('"') || cellText.includes('\n')) {
                        return '"' + cellText.replace(/"/g, '""') + '"';
                    }
                    return cellText;
                }).join(',');
            }).join('\n');
            
            // Add BOM for UTF-8 to support special characters in Excel
            const BOM = '\uFEFF';
            const blob = new Blob([BOM + csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            link.setAttribute('href', url);
            link.setAttribute('download', 'purchase_orders_report_' + new Date().toISOString().split('T')[0] + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }

        function exportToPDF() {
            // Redirect to a PDF export route or use print with PDF option
            // For now, we'll use the browser's print-to-PDF functionality
            window.print();
        }

        function exportToPrint() {
            window.print();
        }

        function openDeleteModal(purchaseOrderId) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = '{{ route("storeowner.ordering.delete-po", ":id") }}'.replace(':id', purchaseOrderId);
            modal.classList.remove('hidden');
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

