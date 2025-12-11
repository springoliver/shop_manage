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
                        <span class="ml-1 hover:text-gray-700">Supplier</span>
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
            <!-- Page Header -->
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Suppliers Settings</h1>

            <!-- Row 1: Shipment Methods and Payment Methods -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Shipment Methods -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-text mr-2"></i> Suppliers shipment methods
                    </h2>
                    
                    <form action="{{ route('storeowner.suppliers.update-shipment') }}" method="POST" id="form-shipment">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Add New
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="shipment" id="shipment" 
                                       value="{{ old('shipment') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                        </div>

                        <!-- Search and Per Page Controls -->
                        <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="shipmentSearchbox"
                                       placeholder="Search shipment methods..." 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select id="shipmentPerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
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
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="shipment" style="cursor: pointer;">
                                            Current shipment methods <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="shipmentTableBody">
                                    @if($productshipments->count() > 0)
                                        @foreach($productshipments as $shipment)
                                            <tr class="shipment-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-shipment="{{ strtolower($shipment->shipment) }}">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $shipment->shipment }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">
                                                    <a href="{{ route('storeowner.suppliers.edit-shipment', base64_encode($shipment->shipmentid)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this shipment method?')) { document.getElementById('delete-form-{{ $shipment->shipmentid }}').submit(); }"
                                                       class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noShipmentsRow">
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No shipment methods found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Client-side Pagination -->
                        <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="shipmentShowingStart">1</span> to <span id="shipmentShowingEnd">10</span> of <span id="shipmentTotalEntries">{{ $productshipments->count() }}</span> entries
                            </div>
                            <div id="shipmentPaginationControls" class="flex items-center gap-2">
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

                    <!-- Hidden delete forms for shipments (outside the Add form to avoid nested forms) -->
                    @if($productshipments->count() > 0)
                        @foreach($productshipments as $shipment)
                            <form id="delete-form-{{ $shipment->shipmentid }}" 
                                  action="{{ route('storeowner.suppliers.delete-shipment', base64_encode($shipment->shipmentid)) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>

                <!-- Payment Methods -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-text mr-2"></i> Suppliers payment methods - Outgoing
                    </h2>
                    
                    <form action="{{ route('storeowner.suppliers.update-payment-method') }}" method="POST" id="form-payment">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Add New
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="paymentmethod" id="paymentmethod" 
                                       value="{{ old('paymentmethod') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                        </div>

                        <!-- Search and Per Page Controls -->
                        <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="paymentSearchbox"
                                       placeholder="Search payment methods..." 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select id="paymentPerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
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
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="payment" style="cursor: pointer;">
                                            Current payment methods <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="paymentTableBody">
                                    @if($purchasePaymentMethods->count() > 0)
                                        @foreach($purchasePaymentMethods as $payment)
                                            <tr class="payment-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-payment="{{ strtolower($payment->paymentmethod) }}">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $payment->paymentmethod }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">
                                                    <a href="{{ route('storeowner.suppliers.edit-payment-method', base64_encode($payment->purchasepaymentmethodid)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this payment method?')) { document.getElementById('delete-form-{{ $payment->purchasepaymentmethodid }}').submit(); }"
                                                       class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noPaymentsRow">
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No payment methods found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Client-side Pagination -->
                        <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="paymentShowingStart">1</span> to <span id="paymentShowingEnd">10</span> of <span id="paymentTotalEntries">{{ $purchasePaymentMethods->count() }}</span> entries
                            </div>
                            <div id="paymentPaginationControls" class="flex items-center gap-2">
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

                    <!-- Hidden delete forms for payment methods (outside the Add form to avoid nested forms) -->
                    @if($purchasePaymentMethods->count() > 0)
                        @foreach($purchasePaymentMethods as $payment)
                            <form id="delete-form-{{ $payment->purchasepaymentmethodid }}" 
                                  action="{{ route('storeowner.suppliers.delete-payment-method', base64_encode($payment->purchasepaymentmethodid)) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Row 2: Product Groups and Product Measures -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Product Groups -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-text mr-2"></i> Product (Catalogue) Groups
                    </h2>
                    
                    <form action="{{ route('storeowner.suppliers.update-catalog-group') }}" method="POST" id="form-catalog">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Add New
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="catalog_product_group_name" id="catalog_product_group_name" 
                                       value="{{ old('catalog_product_group_name') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                        </div>

                        <!-- Search and Per Page Controls -->
                        <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="catalogSearchbox"
                                       placeholder="Search groups..." 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select id="catalogPerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
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
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="group" style="cursor: pointer;">
                                            Current Groups <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="catalogTableBody">
                                    @if($catalogProductGroups->count() > 0)
                                        @foreach($catalogProductGroups as $group)
                                            <tr class="catalog-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-group="{{ strtolower($group->catalog_product_group_name) }}">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $group->catalog_product_group_name }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">
                                                    <a href="{{ route('storeowner.suppliers.edit-catalog-group', base64_encode($group->catalog_product_groupid)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this group?')) { document.getElementById('delete-form-{{ $group->catalog_product_groupid }}').submit(); }"
                                                       class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noCatalogRow">
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No groups found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Client-side Pagination -->
                        <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="catalogShowingStart">1</span> to <span id="catalogShowingEnd">10</span> of <span id="catalogTotalEntries">{{ $catalogProductGroups->count() }}</span> entries
                            </div>
                            <div id="catalogPaginationControls" class="flex items-center gap-2">
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

                    <!-- Hidden delete forms for catalog groups (outside the Add form to avoid nested forms) -->
                    @if($catalogProductGroups->count() > 0)
                        @foreach($catalogProductGroups as $group)
                            <form id="delete-form-{{ $group->catalog_product_groupid }}" 
                                  action="{{ route('storeowner.suppliers.delete-catalog-group', base64_encode($group->catalog_product_groupid)) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>

                <!-- Product Measures -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-text mr-2"></i> Product measures
                    </h2>
                    
                    <form action="{{ route('storeowner.suppliers.update-measure') }}" method="POST" id="form-measure">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Add New
                            </label>
                            <div class="flex gap-2">
                                <input type="text" name="purchasemeasure" id="purchasemeasure" 
                                       value="{{ old('purchasemeasure') }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                        </div>

                        <!-- Search and Per Page Controls -->
                        <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="measureSearchbox"
                                       placeholder="Search measures..." 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select id="measurePerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
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
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="measure" style="cursor: pointer;">
                                            Product measures <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="measureTableBody">
                                    @if($purchaseMeasures->count() > 0)
                                        @foreach($purchaseMeasures as $measure)
                                            <tr class="measure-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-measure="{{ strtolower($measure->purchasemeasure) }}">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $measure->purchasemeasure }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">
                                                    <a href="{{ route('storeowner.suppliers.edit-measure', base64_encode($measure->purchasemeasuresid)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this measure?')) { document.getElementById('delete-form-{{ $measure->purchasemeasuresid }}').submit(); }"
                                                       class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noMeasuresRow">
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No measures found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Client-side Pagination -->
                        <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="measureShowingStart">1</span> to <span id="measureShowingEnd">10</span> of <span id="measureTotalEntries">{{ $purchaseMeasures->count() }}</span> entries
                            </div>
                            <div id="measurePaginationControls" class="flex items-center gap-2">
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

                    <!-- Hidden delete forms for measures (outside the Add form to avoid nested forms) -->
                    @if($purchaseMeasures->count() > 0)
                        @foreach($purchaseMeasures as $measure)
                            <form id="delete-form-{{ $measure->purchasemeasuresid }}" 
                                  action="{{ route('storeowner.suppliers.delete-measure', base64_encode($measure->purchasemeasuresid)) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>                
            </div>

            <!-- Row 3: Tax Settings -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Tax Settings -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">
                        <i class="fas fa-file-text mr-2"></i> Tax settings - Outgoing
                    </h2>
                    
                    <form action="{{ route('storeowner.suppliers.update-tax') }}" method="POST" id="form-tax">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax Name
                            </label>
                            <input type="text" name="tax_name" id="tax_name" 
                                   value="{{ old('tax_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tax Amount - %
                            </label>
                            <input type="text" name="tax_amount" id="tax_amount" 
                                   value="{{ old('tax_amount') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>

                        <!-- Search and Per Page Controls -->
                        <div class="mb-4 flex justify-between items-center flex-wrap gap-4">
                            <div class="flex items-center gap-2">
                                <input type="text" 
                                       id="taxSearchbox"
                                       placeholder="Search tax settings..." 
                                       class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm">
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <label class="text-sm text-gray-700">Show:</label>
                                <select id="taxPerPageSelect" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 min-w-[68px] text-sm">
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
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="taxname" style="cursor: pointer;">
                                            Tax Name <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="pr-0 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-sort="taxamount" style="cursor: pointer;">
                                            Tax Amount <span class="sort-indicator"><i class="fas fa-sort text-gray-400"></i></span>
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200" id="taxTableBody">
                                    @if($taxSettings->count() > 0)
                                        @foreach($taxSettings as $tax)
                                            <tr class="tax-row hover:bg-gray-50" 
                                                data-row-index="{{ $loop->index }}"
                                                data-taxname="{{ strtolower($tax->tax_name) }}"
                                                data-taxamount="{{ strtolower($tax->tax_amount) }}">
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $tax->tax_name }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-900">{{ $tax->tax_amount }}</td>
                                                <td class="px-4 py-3 text-sm font-medium">
                                                    <a href="{{ route('storeowner.suppliers.edit-tax', base64_encode($tax->taxid)) }}" 
                                                       class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="#" 
                                                       onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this tax setting?')) { document.getElementById('delete-form-{{ $tax->taxid }}').submit(); }"
                                                       class="text-red-600 hover:text-red-800" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr id="noTaxRow">
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No tax settings found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- Client-side Pagination -->
                        <div class="mb-4 px-4 py-3 border-t border-gray-200 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing <span id="taxShowingStart">1</span> to <span id="taxShowingEnd">10</span> of <span id="taxTotalEntries">{{ $taxSettings->count() }}</span> entries
                            </div>
                            <div id="taxPaginationControls" class="flex items-center gap-2">
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

                    <!-- Hidden delete forms for tax settings (outside the Add form to avoid nested forms) -->
                    @if($taxSettings->count() > 0)
                        @foreach($taxSettings as $tax)
                            <form id="delete-form-{{ $tax->taxid }}" 
                                  action="{{ route('storeowner.suppliers.delete-tax', base64_encode($tax->taxid)) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </div>
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
        // ============ Shipment Methods Pagination, Search, Sort ============
        let shipmentCurrentPage = 1;
        let shipmentPerPage = 10;
        let shipmentAllRows = [];
        let shipmentFilteredRows = [];
        let shipmentSortColumn = null;
        let shipmentSortDirection = 'asc';

        function initializeShipmentPagination() {
            const tbody = document.getElementById('shipmentTableBody');
            shipmentAllRows = Array.from(tbody.querySelectorAll('tr.shipment-row'));
            shipmentFilteredRows = [...shipmentAllRows];
            
            const noShipmentsRow = document.getElementById('noShipmentsRow');
            if (noShipmentsRow && shipmentAllRows.length > 0) {
                noShipmentsRow.style.display = 'none';
            }
            
            shipmentPerPage = parseInt(document.getElementById('shipmentPerPageSelect').value);
            shipmentCurrentPage = 1;
            updateShipmentDisplay();
        }

        function updateShipmentDisplay() {
            const tbody = document.getElementById('shipmentTableBody');
            shipmentAllRows = Array.from(tbody.querySelectorAll('tr.shipment-row'));
            
            const searchTerm = document.getElementById('shipmentSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                shipmentFilteredRows = shipmentAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                shipmentFilteredRows = [...shipmentAllRows];
            }

            if (shipmentSortColumn) {
                shipmentFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${shipmentSortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${shipmentSortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return shipmentSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return shipmentSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(shipmentFilteredRows.length / shipmentPerPage);
            const start = (shipmentCurrentPage - 1) * shipmentPerPage;
            const end = Math.min(start + shipmentPerPage, shipmentFilteredRows.length);

            if (shipmentSortColumn && shipmentFilteredRows.length > 0) {
                const noShipmentsRow = document.getElementById('noShipmentsRow');
                shipmentAllRows.forEach(row => {
                    if (row.id !== 'noShipmentsRow') {
                        row.remove();
                    }
                });
                
                shipmentFilteredRows.forEach(row => {
                    if (row.id !== 'noShipmentsRow') {
                        if (noShipmentsRow && noShipmentsRow.parentNode) {
                            tbody.insertBefore(row, noShipmentsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                shipmentAllRows = Array.from(tbody.querySelectorAll('tr.shipment-row'));
                const sortedFilteredIndices = shipmentFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                shipmentAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                shipmentFilteredRows = newFilteredRows;
            }

            shipmentAllRows.forEach(row => {
                if (row.id !== 'noShipmentsRow') {
                    row.style.display = 'none';
                }
            });

            const noShipmentsRow = document.getElementById('noShipmentsRow');
            if (noShipmentsRow) {
                if (shipmentFilteredRows.length === 0) {
                    noShipmentsRow.style.display = '';
                } else {
                    noShipmentsRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (shipmentFilteredRows[i] && shipmentFilteredRows[i].id !== 'noShipmentsRow') {
                    shipmentFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('shipmentShowingStart').textContent = shipmentFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('shipmentShowingEnd').textContent = end;
            document.getElementById('shipmentTotalEntries').textContent = shipmentFilteredRows.length;

            generateShipmentPaginationControls(totalPages);
        }

        function generateShipmentPaginationControls(totalPages) {
            const paginationDiv = document.getElementById('shipmentPaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (shipmentCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = shipmentCurrentPage === 1;
            prevBtn.onclick = () => {
                if (shipmentCurrentPage > 1) {
                    shipmentCurrentPage--;
                    updateShipmentDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, shipmentCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    shipmentCurrentPage = 1;
                    updateShipmentDisplay();
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
                    (i === shipmentCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    shipmentCurrentPage = i;
                    updateShipmentDisplay();
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
                    shipmentCurrentPage = totalPages;
                    updateShipmentDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (shipmentCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = shipmentCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (shipmentCurrentPage < totalPages) {
                    shipmentCurrentPage++;
                    updateShipmentDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortShipmentTable(column) {
            if (shipmentSortColumn === column) {
                shipmentSortDirection = shipmentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                shipmentSortColumn = column;
                shipmentSortDirection = 'asc';
            }

            const shipmentTable = document.querySelector('#shipmentTableBody').closest('table');
            shipmentTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = shipmentTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = shipmentSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            shipmentCurrentPage = 1;
            updateShipmentDisplay();
        }

        // ============ Payment Methods Pagination, Search, Sort ============
        let paymentCurrentPage = 1;
        let paymentPerPage = 10;
        let paymentAllRows = [];
        let paymentFilteredRows = [];
        let paymentSortColumn = null;
        let paymentSortDirection = 'asc';

        function initializePaymentPagination() {
            const tbody = document.getElementById('paymentTableBody');
            paymentAllRows = Array.from(tbody.querySelectorAll('tr.payment-row'));
            paymentFilteredRows = [...paymentAllRows];
            
            const noPaymentsRow = document.getElementById('noPaymentsRow');
            if (noPaymentsRow && paymentAllRows.length > 0) {
                noPaymentsRow.style.display = 'none';
            }
            
            paymentPerPage = parseInt(document.getElementById('paymentPerPageSelect').value);
            paymentCurrentPage = 1;
            updatePaymentDisplay();
        }

        function updatePaymentDisplay() {
            const tbody = document.getElementById('paymentTableBody');
            paymentAllRows = Array.from(tbody.querySelectorAll('tr.payment-row'));
            
            const searchTerm = document.getElementById('paymentSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                paymentFilteredRows = paymentAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                paymentFilteredRows = [...paymentAllRows];
            }

            if (paymentSortColumn) {
                paymentFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${paymentSortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${paymentSortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return paymentSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return paymentSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(paymentFilteredRows.length / paymentPerPage);
            const start = (paymentCurrentPage - 1) * paymentPerPage;
            const end = Math.min(start + paymentPerPage, paymentFilteredRows.length);

            if (paymentSortColumn && paymentFilteredRows.length > 0) {
                const noPaymentsRow = document.getElementById('noPaymentsRow');
                paymentAllRows.forEach(row => {
                    if (row.id !== 'noPaymentsRow') {
                        row.remove();
                    }
                });
                
                paymentFilteredRows.forEach(row => {
                    if (row.id !== 'noPaymentsRow') {
                        if (noPaymentsRow && noPaymentsRow.parentNode) {
                            tbody.insertBefore(row, noPaymentsRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                paymentAllRows = Array.from(tbody.querySelectorAll('tr.payment-row'));
                const sortedFilteredIndices = paymentFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                paymentAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                paymentFilteredRows = newFilteredRows;
            }

            paymentAllRows.forEach(row => {
                if (row.id !== 'noPaymentsRow') {
                    row.style.display = 'none';
                }
            });

            const noPaymentsRow = document.getElementById('noPaymentsRow');
            if (noPaymentsRow) {
                if (paymentFilteredRows.length === 0) {
                    noPaymentsRow.style.display = '';
                } else {
                    noPaymentsRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (paymentFilteredRows[i] && paymentFilteredRows[i].id !== 'noPaymentsRow') {
                    paymentFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('paymentShowingStart').textContent = paymentFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('paymentShowingEnd').textContent = end;
            document.getElementById('paymentTotalEntries').textContent = paymentFilteredRows.length;

            generatePaymentPaginationControls(totalPages);
        }

        function generatePaymentPaginationControls(totalPages) {
            const paginationDiv = document.getElementById('paymentPaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (paymentCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = paymentCurrentPage === 1;
            prevBtn.onclick = () => {
                if (paymentCurrentPage > 1) {
                    paymentCurrentPage--;
                    updatePaymentDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, paymentCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    paymentCurrentPage = 1;
                    updatePaymentDisplay();
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
                    (i === paymentCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    paymentCurrentPage = i;
                    updatePaymentDisplay();
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
                    paymentCurrentPage = totalPages;
                    updatePaymentDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (paymentCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = paymentCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (paymentCurrentPage < totalPages) {
                    paymentCurrentPage++;
                    updatePaymentDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortPaymentTable(column) {
            if (paymentSortColumn === column) {
                paymentSortDirection = paymentSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                paymentSortColumn = column;
                paymentSortDirection = 'asc';
            }

            const paymentTable = document.querySelector('#paymentTableBody').closest('table');
            paymentTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = paymentTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = paymentSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            paymentCurrentPage = 1;
            updatePaymentDisplay();
        }

        // ============ Catalog Product Groups Pagination, Search, Sort ============
        let catalogCurrentPage = 1;
        let catalogPerPage = 10;
        let catalogAllRows = [];
        let catalogFilteredRows = [];
        let catalogSortColumn = null;
        let catalogSortDirection = 'asc';

        function initializeCatalogPagination() {
            const tbody = document.getElementById('catalogTableBody');
            catalogAllRows = Array.from(tbody.querySelectorAll('tr.catalog-row'));
            catalogFilteredRows = [...catalogAllRows];
            
            const noCatalogRow = document.getElementById('noCatalogRow');
            if (noCatalogRow && catalogAllRows.length > 0) {
                noCatalogRow.style.display = 'none';
            }
            
            catalogPerPage = parseInt(document.getElementById('catalogPerPageSelect').value);
            catalogCurrentPage = 1;
            updateCatalogDisplay();
        }

        function updateCatalogDisplay() {
            const tbody = document.getElementById('catalogTableBody');
            catalogAllRows = Array.from(tbody.querySelectorAll('tr.catalog-row'));
            
            const searchTerm = document.getElementById('catalogSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                catalogFilteredRows = catalogAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                catalogFilteredRows = [...catalogAllRows];
            }

            if (catalogSortColumn) {
                catalogFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${catalogSortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${catalogSortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return catalogSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return catalogSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(catalogFilteredRows.length / catalogPerPage);
            const start = (catalogCurrentPage - 1) * catalogPerPage;
            const end = Math.min(start + catalogPerPage, catalogFilteredRows.length);

            if (catalogSortColumn && catalogFilteredRows.length > 0) {
                const noCatalogRow = document.getElementById('noCatalogRow');
                catalogAllRows.forEach(row => {
                    if (row.id !== 'noCatalogRow') {
                        row.remove();
                    }
                });
                
                catalogFilteredRows.forEach(row => {
                    if (row.id !== 'noCatalogRow') {
                        if (noCatalogRow && noCatalogRow.parentNode) {
                            tbody.insertBefore(row, noCatalogRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                catalogAllRows = Array.from(tbody.querySelectorAll('tr.catalog-row'));
                const sortedFilteredIndices = catalogFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                catalogAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                catalogFilteredRows = newFilteredRows;
            }

            catalogAllRows.forEach(row => {
                if (row.id !== 'noCatalogRow') {
                    row.style.display = 'none';
                }
            });

            const noCatalogRow = document.getElementById('noCatalogRow');
            if (noCatalogRow) {
                if (catalogFilteredRows.length === 0) {
                    noCatalogRow.style.display = '';
                } else {
                    noCatalogRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (catalogFilteredRows[i] && catalogFilteredRows[i].id !== 'noCatalogRow') {
                    catalogFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('catalogShowingStart').textContent = catalogFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('catalogShowingEnd').textContent = end;
            document.getElementById('catalogTotalEntries').textContent = catalogFilteredRows.length;

            generateCatalogPaginationControls(totalPages);
        }

        function generateCatalogPaginationControls(totalPages) {
            const paginationDiv = document.getElementById('catalogPaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (catalogCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = catalogCurrentPage === 1;
            prevBtn.onclick = () => {
                if (catalogCurrentPage > 1) {
                    catalogCurrentPage--;
                    updateCatalogDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, catalogCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    catalogCurrentPage = 1;
                    updateCatalogDisplay();
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
                    (i === catalogCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    catalogCurrentPage = i;
                    updateCatalogDisplay();
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
                    catalogCurrentPage = totalPages;
                    updateCatalogDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (catalogCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = catalogCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (catalogCurrentPage < totalPages) {
                    catalogCurrentPage++;
                    updateCatalogDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortCatalogTable(column) {
            if (catalogSortColumn === column) {
                catalogSortDirection = catalogSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                catalogSortColumn = column;
                catalogSortDirection = 'asc';
            }

            const catalogTable = document.querySelector('#catalogTableBody').closest('table');
            catalogTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = catalogTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = catalogSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            catalogCurrentPage = 1;
            updateCatalogDisplay();
        }

        // ============ Product Measures Pagination, Search, Sort ============
        let measureCurrentPage = 1;
        let measurePerPage = 10;
        let measureAllRows = [];
        let measureFilteredRows = [];
        let measureSortColumn = null;
        let measureSortDirection = 'asc';

        function initializeMeasurePagination() {
            const tbody = document.getElementById('measureTableBody');
            measureAllRows = Array.from(tbody.querySelectorAll('tr.measure-row'));
            measureFilteredRows = [...measureAllRows];
            
            const noMeasuresRow = document.getElementById('noMeasuresRow');
            if (noMeasuresRow && measureAllRows.length > 0) {
                noMeasuresRow.style.display = 'none';
            }
            
            measurePerPage = parseInt(document.getElementById('measurePerPageSelect').value);
            measureCurrentPage = 1;
            updateMeasureDisplay();
        }

        function updateMeasureDisplay() {
            const tbody = document.getElementById('measureTableBody');
            measureAllRows = Array.from(tbody.querySelectorAll('tr.measure-row'));
            
            const searchTerm = document.getElementById('measureSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                measureFilteredRows = measureAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                measureFilteredRows = [...measureAllRows];
            }

            if (measureSortColumn) {
                measureFilteredRows.sort((a, b) => {
                    const aValue = a.getAttribute(`data-${measureSortColumn}`) || '';
                    const bValue = b.getAttribute(`data-${measureSortColumn}`) || '';
                    
                    if (aValue < bValue) {
                        return measureSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return measureSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(measureFilteredRows.length / measurePerPage);
            const start = (measureCurrentPage - 1) * measurePerPage;
            const end = Math.min(start + measurePerPage, measureFilteredRows.length);

            if (measureSortColumn && measureFilteredRows.length > 0) {
                const noMeasuresRow = document.getElementById('noMeasuresRow');
                measureAllRows.forEach(row => {
                    if (row.id !== 'noMeasuresRow') {
                        row.remove();
                    }
                });
                
                measureFilteredRows.forEach(row => {
                    if (row.id !== 'noMeasuresRow') {
                        if (noMeasuresRow && noMeasuresRow.parentNode) {
                            tbody.insertBefore(row, noMeasuresRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                measureAllRows = Array.from(tbody.querySelectorAll('tr.measure-row'));
                const sortedFilteredIndices = measureFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                measureAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                measureFilteredRows = newFilteredRows;
            }

            measureAllRows.forEach(row => {
                if (row.id !== 'noMeasuresRow') {
                    row.style.display = 'none';
                }
            });

            const noMeasuresRow = document.getElementById('noMeasuresRow');
            if (noMeasuresRow) {
                if (measureFilteredRows.length === 0) {
                    noMeasuresRow.style.display = '';
                } else {
                    noMeasuresRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (measureFilteredRows[i] && measureFilteredRows[i].id !== 'noMeasuresRow') {
                    measureFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('measureShowingStart').textContent = measureFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('measureShowingEnd').textContent = end;
            document.getElementById('measureTotalEntries').textContent = measureFilteredRows.length;

            generateMeasurePaginationControls(totalPages);
        }

        function generateMeasurePaginationControls(totalPages) {
            const paginationDiv = document.getElementById('measurePaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (measureCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = measureCurrentPage === 1;
            prevBtn.onclick = () => {
                if (measureCurrentPage > 1) {
                    measureCurrentPage--;
                    updateMeasureDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, measureCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    measureCurrentPage = 1;
                    updateMeasureDisplay();
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
                    (i === measureCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    measureCurrentPage = i;
                    updateMeasureDisplay();
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
                    measureCurrentPage = totalPages;
                    updateMeasureDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (measureCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = measureCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (measureCurrentPage < totalPages) {
                    measureCurrentPage++;
                    updateMeasureDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortMeasureTable(column) {
            if (measureSortColumn === column) {
                measureSortDirection = measureSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                measureSortColumn = column;
                measureSortDirection = 'asc';
            }

            const measureTable = document.querySelector('#measureTableBody').closest('table');
            measureTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = measureTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = measureSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            measureCurrentPage = 1;
            updateMeasureDisplay();
        }

        // ============ Tax Settings Pagination, Search, Sort ============
        let taxCurrentPage = 1;
        let taxPerPage = 10;
        let taxAllRows = [];
        let taxFilteredRows = [];
        let taxSortColumn = null;
        let taxSortDirection = 'asc';

        function initializeTaxPagination() {
            const tbody = document.getElementById('taxTableBody');
            taxAllRows = Array.from(tbody.querySelectorAll('tr.tax-row'));
            taxFilteredRows = [...taxAllRows];
            
            const noTaxRow = document.getElementById('noTaxRow');
            if (noTaxRow && taxAllRows.length > 0) {
                noTaxRow.style.display = 'none';
            }
            
            taxPerPage = parseInt(document.getElementById('taxPerPageSelect').value);
            taxCurrentPage = 1;
            updateTaxDisplay();
        }

        function updateTaxDisplay() {
            const tbody = document.getElementById('taxTableBody');
            taxAllRows = Array.from(tbody.querySelectorAll('tr.tax-row'));
            
            const searchTerm = document.getElementById('taxSearchbox')?.value.toLowerCase() || '';
            
            if (searchTerm) {
                taxFilteredRows = taxAllRows.filter(row => {
                    const text = row.textContent.toLowerCase();
                    return text.includes(searchTerm);
                });
            } else {
                taxFilteredRows = [...taxAllRows];
            }

            if (taxSortColumn) {
                taxFilteredRows.sort((a, b) => {
                    let aValue = a.getAttribute(`data-${taxSortColumn}`) || '';
                    let bValue = b.getAttribute(`data-${taxSortColumn}`) || '';
                    
                    // Handle numeric sorting for tax amount
                    if (taxSortColumn === 'taxamount') {
                        aValue = parseFloat(aValue) || 0;
                        bValue = parseFloat(bValue) || 0;
                        return taxSortDirection === 'asc' ? aValue - bValue : bValue - aValue;
                    }
                    
                    // String comparison
                    if (aValue < bValue) {
                        return taxSortDirection === 'asc' ? -1 : 1;
                    }
                    if (aValue > bValue) {
                        return taxSortDirection === 'asc' ? 1 : -1;
                    }
                    return 0;
                });
            }

            const totalPages = Math.ceil(taxFilteredRows.length / taxPerPage);
            const start = (taxCurrentPage - 1) * taxPerPage;
            const end = Math.min(start + taxPerPage, taxFilteredRows.length);

            if (taxSortColumn && taxFilteredRows.length > 0) {
                const noTaxRow = document.getElementById('noTaxRow');
                taxAllRows.forEach(row => {
                    if (row.id !== 'noTaxRow') {
                        row.remove();
                    }
                });
                
                taxFilteredRows.forEach(row => {
                    if (row.id !== 'noTaxRow') {
                        if (noTaxRow && noTaxRow.parentNode) {
                            tbody.insertBefore(row, noTaxRow);
                        } else {
                            tbody.appendChild(row);
                        }
                    }
                });
                
                taxAllRows = Array.from(tbody.querySelectorAll('tr.tax-row'));
                const sortedFilteredIndices = taxFilteredRows.map(row => row.getAttribute('data-row-index'));
                const newFilteredRows = [];
                taxAllRows.forEach(row => {
                    const rowIndex = row.getAttribute('data-row-index');
                    if (sortedFilteredIndices.includes(rowIndex)) {
                        newFilteredRows.push(row);
                    }
                });
                taxFilteredRows = newFilteredRows;
            }

            taxAllRows.forEach(row => {
                if (row.id !== 'noTaxRow') {
                    row.style.display = 'none';
                }
            });

            const noTaxRow = document.getElementById('noTaxRow');
            if (noTaxRow) {
                if (taxFilteredRows.length === 0) {
                    noTaxRow.style.display = '';
                } else {
                    noTaxRow.style.display = 'none';
                }
            }

            for (let i = start; i < end; i++) {
                if (taxFilteredRows[i] && taxFilteredRows[i].id !== 'noTaxRow') {
                    taxFilteredRows[i].style.display = '';
                }
            }

            document.getElementById('taxShowingStart').textContent = taxFilteredRows.length === 0 ? 0 : start + 1;
            document.getElementById('taxShowingEnd').textContent = end;
            document.getElementById('taxTotalEntries').textContent = taxFilteredRows.length;

            generateTaxPaginationControls(totalPages);
        }

        function generateTaxPaginationControls(totalPages) {
            const paginationDiv = document.getElementById('taxPaginationControls');
            paginationDiv.innerHTML = '';

            if (totalPages <= 1) {
                return;
            }

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Previous';
            prevBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (taxCurrentPage === 1 ? ' opacity-50 cursor-not-allowed' : '');
            prevBtn.disabled = taxCurrentPage === 1;
            prevBtn.onclick = () => {
                if (taxCurrentPage > 1) {
                    taxCurrentPage--;
                    updateTaxDisplay();
                }
            };
            paginationDiv.appendChild(prevBtn);

            const maxVisible = 5;
            let startPage = Math.max(1, taxCurrentPage - Math.floor(maxVisible / 2));
            let endPage = Math.min(totalPages, startPage + maxVisible - 1);
            
            if (endPage - startPage < maxVisible - 1) {
                startPage = Math.max(1, endPage - maxVisible + 1);
            }

            if (startPage > 1) {
                const firstBtn = document.createElement('button');
                firstBtn.textContent = '1';
                firstBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100';
                firstBtn.onclick = () => {
                    taxCurrentPage = 1;
                    updateTaxDisplay();
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
                    (i === taxCurrentPage ? 'bg-gray-800 text-white' : 'hover:bg-gray-100');
                pageBtn.onclick = () => {
                    taxCurrentPage = i;
                    updateTaxDisplay();
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
                    taxCurrentPage = totalPages;
                    updateTaxDisplay();
                };
                paginationDiv.appendChild(lastBtn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Next';
            nextBtn.className = 'px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-100' + (taxCurrentPage === totalPages ? ' opacity-50 cursor-not-allowed' : '');
            nextBtn.disabled = taxCurrentPage === totalPages;
            nextBtn.onclick = () => {
                if (taxCurrentPage < totalPages) {
                    taxCurrentPage++;
                    updateTaxDisplay();
                }
            };
            paginationDiv.appendChild(nextBtn);
        }

        function sortTaxTable(column) {
            if (taxSortColumn === column) {
                taxSortDirection = taxSortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                taxSortColumn = column;
                taxSortDirection = 'asc';
            }

            const taxTable = document.querySelector('#taxTableBody').closest('table');
            taxTable.querySelectorAll('.sortable .sort-indicator').forEach(indicator => {
                indicator.innerHTML = '<i class="fas fa-sort text-gray-400"></i>';
            });

            const clickedHeader = taxTable.querySelector(`th[data-sort="${column}"]`);
            if (clickedHeader) {
                const indicator = clickedHeader.querySelector('.sort-indicator');
                if (indicator) {
                    indicator.innerHTML = taxSortDirection === 'asc' 
                        ? '<i class="fas fa-sort-up text-gray-800"></i>' 
                        : '<i class="fas fa-sort-down text-gray-800"></i>';
                }
            }

            taxCurrentPage = 1;
            updateTaxDisplay();
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeShipmentPagination();
            initializePaymentPagination();
            initializeCatalogPagination();
            initializeMeasurePagination();
            initializeTaxPagination();

            // Shipment search and per page
            document.getElementById('shipmentSearchbox')?.addEventListener('keyup', function() {
                shipmentCurrentPage = 1;
                updateShipmentDisplay();
            });
            document.getElementById('shipmentPerPageSelect')?.addEventListener('change', function() {
                shipmentPerPage = parseInt(this.value);
                shipmentCurrentPage = 1;
                updateShipmentDisplay();
            });
            const shipmentTable = document.querySelector('#shipmentTableBody').closest('table');
            if (shipmentTable) {
                shipmentTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortShipmentTable(column);
                        }
                    });
                });
            }

            // Payment search and per page
            document.getElementById('paymentSearchbox')?.addEventListener('keyup', function() {
                paymentCurrentPage = 1;
                updatePaymentDisplay();
            });
            document.getElementById('paymentPerPageSelect')?.addEventListener('change', function() {
                paymentPerPage = parseInt(this.value);
                paymentCurrentPage = 1;
                updatePaymentDisplay();
            });
            const paymentTable = document.querySelector('#paymentTableBody').closest('table');
            if (paymentTable) {
                paymentTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortPaymentTable(column);
                        }
                    });
                });
            }

            // Catalog search and per page
            document.getElementById('catalogSearchbox')?.addEventListener('keyup', function() {
                catalogCurrentPage = 1;
                updateCatalogDisplay();
            });
            document.getElementById('catalogPerPageSelect')?.addEventListener('change', function() {
                catalogPerPage = parseInt(this.value);
                catalogCurrentPage = 1;
                updateCatalogDisplay();
            });
            const catalogTable = document.querySelector('#catalogTableBody').closest('table');
            if (catalogTable) {
                catalogTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortCatalogTable(column);
                        }
                    });
                });
            }

            // Measure search and per page
            document.getElementById('measureSearchbox')?.addEventListener('keyup', function() {
                measureCurrentPage = 1;
                updateMeasureDisplay();
            });
            document.getElementById('measurePerPageSelect')?.addEventListener('change', function() {
                measurePerPage = parseInt(this.value);
                measureCurrentPage = 1;
                updateMeasureDisplay();
            });
            const measureTable = document.querySelector('#measureTableBody').closest('table');
            if (measureTable) {
                measureTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortMeasureTable(column);
                        }
                    });
                });
            }

            // Tax search and per page
            document.getElementById('taxSearchbox')?.addEventListener('keyup', function() {
                taxCurrentPage = 1;
                updateTaxDisplay();
            });
            document.getElementById('taxPerPageSelect')?.addEventListener('change', function() {
                taxPerPage = parseInt(this.value);
                taxCurrentPage = 1;
                updateTaxDisplay();
            });
            const taxTable = document.querySelector('#taxTableBody').closest('table');
            if (taxTable) {
                taxTable.querySelectorAll('.sortable').forEach(header => {
                    header.addEventListener('click', function() {
                        const column = this.getAttribute('data-sort');
                        if (column) {
                            sortTaxTable(column);
                        }
                    });
                });
            }
        });
    </script>
    @endpush

</x-storeowner-app-layout>

