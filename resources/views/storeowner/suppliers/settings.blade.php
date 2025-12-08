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

                        <div class="mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current shipment methods</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($productshipments->count() > 0)
                                        @foreach($productshipments as $shipment)
                                            <tr>
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
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No shipment methods found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

                        <div class="mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current payment methods</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($purchasePaymentMethods->count() > 0)
                                        @foreach($purchasePaymentMethods as $payment)
                                            <tr>
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
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No payment methods found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

            <!-- Row 2: Product Groups, Product Measures, and Tax Settings -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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

                        <div class="mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Groups</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($catalogProductGroups->count() > 0)
                                        @foreach($catalogProductGroups as $group)
                                            <tr>
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
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No groups found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

                        <div class="mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product measures</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($purchaseMeasures->count() > 0)
                                        @foreach($purchaseMeasures as $measure)
                                            <tr>
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
                                        <tr>
                                            <td colspan="2" class="px-4 py-3 text-sm text-gray-500 text-center">No measures found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

                        <div class="mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Name</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tax Amount</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @if($taxSettings->count() > 0)
                                        @foreach($taxSettings as $tax)
                                            <tr>
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
                                        <tr>
                                            <td colspan="3" class="px-4 py-3 text-sm text-gray-500 text-center">No tax settings found.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
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

</x-storeowner-app-layout>

