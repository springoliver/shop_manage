@section('page_header', 'Monthly Orders by Supplier')
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
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. of Products</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tax Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Inc. Tax</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Docket</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No:</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Note</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($reports as $report)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($report->supplier && $report->supplier->supplierid)
                                            <a href="{{ route('storeowner.ordering.supplier_all_invoices', base64_encode($report->supplier->supplierid)) }}" 
                                               class="text-blue-600 hover:underline cursor-pointer"
                                               title="Supplier">
                                                {{ $report->supplier->supplier_name ?? 'N/A' }}
                                            </a>
                                        @else
                                            {{ $report->supplier->supplier_name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->delivery_date ? $report->delivery_date->format('Y-m-d') : '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        @if($report->delivery_date && $report->supplier && $report->supplier->supplierid)
                                            <a href="{{ route('storeowner.ordering.supplier_all_invoices_monthly', [
                                                'supplierid' => base64_encode($report->supplier->supplierid),
                                                'delivery_date' => $report->delivery_date->format('Y-m-d')
                                            ]) }}" 
                                               class="text-blue-600 hover:underline cursor-pointer"
                                               title="">
                                                {{ $report->delivery_date->format('F - Y') }}
                                            </a>
                                        @elseif($report->delivery_date)
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
                                                <a href="{{ route('storeowner.ordering.edit', $report->purchase_orders_id) }}" 
                                                   class="text-white hover:underline"
                                                   style="color:#fff;">Missing</a>
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exist</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $report->invoicenumber ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($report->creditnote == 'Yes')
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <a href="{{ route('storeowner.ordering.edit', $report->purchase_orders_id) }}" 
                                                   class="text-white hover:underline cursor-pointer"
                                                   style="color:#fff;"
                                                   title="Credit Note">Credit Note</a>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-center">
                                        <div class="flex space-x-2 justify-center">
                                            <a href="{{ route('storeowner.ordering.edit', $report->purchase_orders_id) }}" 
                                               class="text-blue-600 hover:text-blue-800 cursor-pointer" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               class="text-red-600 hover:text-red-800 cursor-pointer" 
                                               title="Delete"
                                               data-href="{{ route('storeowner.ordering.delete-po', base64_encode($report->purchase_orders_id)) }}"
                                               onclick="event.preventDefault(); openDeleteModal('{{ base64_encode($report->purchase_orders_id) }}');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-3 text-center text-sm text-gray-500">
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

    @push('scripts')
    <script>
        function openDeleteModal(purchaseOrderId) {
            const modal = document.getElementById('deleteModal');
            const form = document.getElementById('deleteForm');
            form.action = '{{ url("storeowner/ordering/delete-po") }}/' + purchaseOrderId;
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

