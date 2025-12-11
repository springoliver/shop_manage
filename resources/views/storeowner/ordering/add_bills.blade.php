@section('page_header', 'Edit Bill')
<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-3">
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
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Add Bills
                    </a>
                </div>
                <div class="flex space-x-2 border-b border-gray-200">
                    <a href="{{ route('storeowner.ordering.reports_chart_yearly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Yearly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_monthly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Monthly Chart View
                    </a>
                    <a href="{{ route('storeowner.ordering.reports_chart_weekly') }}" 
                       class="inline-block px-4 py-2 text-gray-600 hover:text-blue-600 hover:border-b-2 hover:border-blue-600">
                        Weekly Chart View
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Add Bill Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-800">
                                <i class="fas fa-file-text mr-2"></i> Add Bills for - {{ $delivery_date ? date('F - Y', strtotime($delivery_date)) : '' }}
                            </h3>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('storeowner.ordering.new_bill') }}" method="POST" id="myform" name="myform">
                                @csrf
                                
                                @if(isset($purchaseOrderEdit))
                                    <input type="hidden" name="purchase_orders_id" value="{{ base64_encode($purchaseOrderEdit->purchase_orders_id) }}">
                                @endif

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Category:</label>
                                    <select name="categoryid" id="categoryid" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach($purchaseOrdersCategory as $category)
                                            <option value="{{ $category->categoryid }}" {{ (isset($purchaseOrderEdit) && $purchaseOrderEdit->categoryid == $category->categoryid) ? 'selected' : '' }}>
                                                {{ $category->category_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title:</label>
                                        <input type="text" 
                                               name="products_bought" 
                                               id="products_bought"
                                               value="{{ isset($purchaseOrderEdit) ? $purchaseOrderEdit->products_bought : '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice No:</label>
                                        <input type="text" 
                                               name="invoicenumber" 
                                               id="invoicenumber"
                                               value="{{ isset($purchaseOrderEdit) ? $purchaseOrderEdit->invoicenumber : '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Invoice exist:</label>
                                        <div class="mt-2">
                                            <label class="inline-flex items-center mr-4">
                                                <input type="radio" name="invoicestatus" value="Yes" {{ (!isset($purchaseOrderEdit) || $purchaseOrderEdit->invoicestatus == 'Yes') ? 'checked' : '' }} class="form-radio">
                                                <span class="ml-2">Yes</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" name="invoicestatus" value="No" {{ (isset($purchaseOrderEdit) && $purchaseOrderEdit->invoicestatus == 'No') ? 'checked' : '' }} class="form-radio">
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
                                               value="{{ isset($purchaseOrderEdit) ? $purchaseOrderEdit->total_amount : '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Tax:</label>
                                        <input type="number" 
                                               step="0.01"
                                               name="total_tax" 
                                               id="total_tax"
                                               value="{{ isset($purchaseOrderEdit) ? $purchaseOrderEdit->total_tax : '' }}"
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
                                               value="{{ isset($purchaseOrderEdit) ? $purchaseOrderEdit->amount_inc_tax : '' }}"
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                               required>
                                        <input type="hidden" name="delivery_date" value="{{ $delivery_date }}">
                                    </div>
                                </div>

                                <div class="flex space-x-3">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                        Save
                                    </button>
                                    <a href="{{ route('storeowner.ordering.tax_analysis') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
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
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-file-text mr-2"></i> Bills
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="table-report">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Desc</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Tax Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Inc. Tax</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice No:</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($purchaseOrders as $order)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $order->purchase_orders_type }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $order->category->category_name ?? 'Purchasing' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $order->supplier->supplier_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $order->products_bought ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        {{ $order->delivery_date ? $order->delivery_date->format('Y-m-d') : '' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">
                                        <a href="{{ route('storeowner.ordering.add_bills', base64_encode($order->delivery_date)) }}" 
                                           class="text-blue-600 hover:underline">
                                            {{ $order->delivery_date ? $order->delivery_date->format('F - Y') : '' }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($order->total_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($order->total_amount ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">€ {{ number_format($order->amount_inc_tax ?? 0, 2) }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($order->invoicestatus == 'No')
                                            <a href="{{ route('storeowner.ordering.edit_bills', [
                                                'purchase_orders_id' => base64_encode($order->purchase_orders_id),
                                                'delivery_date' => base64_encode($order->delivery_date ? $order->delivery_date->format('Y-m-d') : '')
                                            ]) }}" 
                                               class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 hover:bg-yellow-200 inline-block">
                                                Missing
                                            </a>
                                        @else
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Exist</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $order->invoicenumber ?? '' }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('storeowner.ordering.edit_bills', [
                                                'purchase_orders_id' => base64_encode($order->purchase_orders_id),
                                                'delivery_date' => base64_encode($order->delivery_date ? $order->delivery_date->format('Y-m-d') : '')
                                            ]) }}" 
                                               class="text-blue-600 hover:text-blue-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               class="text-red-600 hover:text-red-800" 
                                               title="Delete"
                                               onclick="event.preventDefault(); openDeleteModal('{{ route('storeowner.ordering.delete-po', base64_encode($order->purchase_orders_id)) }}');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="px-4 py-3 text-center text-sm text-gray-500">
                                        No bills found
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
    </script>
    @endpush
</x-storeowner-app-layout>

