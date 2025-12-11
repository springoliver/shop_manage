@section('page_header', 'Credit Notes')
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

            <form action="{{ route('storeowner.ordering.credit_notes') }}" method="POST" id="myform" name="myform">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
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
                           class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
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
                           class="inline-block px-4 py-2 text-blue-600 border-b-2 border-blue-600 font-medium">
                            Credit Notes
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Report Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-text mr-2"></i> Orders Reporting
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-report">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. of Products</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tax Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Inc. Tax</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Docket</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->supplier->supplier_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->delivery_date ? $report->delivery_date->format('Y-m-d') : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->total_products ?? 0 }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->total_amount ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($report->amount_inc_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($report->deliverydocketstatus == 'No')
                                            <button type="button" 
                                                    class="px-2 py-1 text-xs font-semibold rounded bg-red-600 text-white hover:bg-red-700"
                                                    onclick="openStatusModal('{{ $report->purchase_orders_id }}', '{{ $report->deliverydocketstatus }}')">
                                                Missing
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="px-2 py-1 text-xs font-semibold rounded bg-yellow-600 text-white hover:bg-yellow-700"
                                                    onclick="openStatusModal('{{ $report->purchase_orders_id }}', '{{ $report->deliverydocketstatus }}')">
                                                Exist
                                            </button>
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
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->creditnotedesc ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <div class="flex space-x-2">
                                            <a href="#" 
                                               class="text-blue-600 hover:text-blue-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               class="text-red-600 hover:text-red-800 delete-order" 
                                               data-href="#"
                                               title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="px-4 py-3 text-center text-sm text-gray-500">
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

    <!-- Status Change Modal -->
    <div id="statusModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Delivery Docket Status</h3>
                    <button onclick="closeStatusModal()" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="statusForm" action="{{ route('storeowner.ordering.update_delivery_dock_status') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="inline-flex items-center mr-6">
                            <input type="radio" name="deliverydocketstatus" value="Yes" class="form-radio" id="statusYes">
                            <span class="ml-2">Exist</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="deliverydocketstatus" value="No" class="form-radio" id="statusNo">
                            <span class="ml-2">Missing</span>
                        </label>
                        <input type="hidden" name="purchase_orders_id" id="modalPurchaseOrderId">
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeStatusModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openStatusModal(purchaseOrderId, currentStatus) {
            document.getElementById('modalPurchaseOrderId').value = purchaseOrderId;
            
            if (currentStatus === 'Yes') {
                document.getElementById('statusYes').checked = true;
            } else {
                document.getElementById('statusNo').checked = true;
            }
            
            document.getElementById('statusModal').classList.remove('hidden');
        }

        function closeStatusModal() {
            document.getElementById('statusModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('statusModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeStatusModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeStatusModal();
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

