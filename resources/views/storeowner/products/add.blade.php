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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

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
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-file-text-o mr-2"></i> Products
                </h2>
                
                <form action="{{ route('storeowner.products.update') }}" method="POST" id="myform">
                    @csrf
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Group <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="catalog_product_groupid" id="catalog_product_groupid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Catalogue Group</option>
                                @foreach($catalogProductGroups as $group)
                                    <option value="{{ $group->catalog_product_groupid }}" {{ old('catalog_product_groupid') == $group->catalog_product_groupid ? 'selected' : '' }}>
                                        {{ $group->catalog_product_group_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Department <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="departmentid" id="departmentid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Department</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->departmentid }}" {{ old('departmentid') == $department->departmentid ? 'selected' : '' }}>
                                        {{ $department->department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Product Supplier <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="supplierid" id="supplierid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Supplier</option>
                                @foreach($storeSuppliers as $supplier)
                                    <option value="{{ $supplier->supplierid }}" {{ old('supplierid') == $supplier->supplierid ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Delivery Method <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="shipmentid" id="shipmentid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Delivery Method</option>
                                @foreach($productshipments as $shipment)
                                    <option value="{{ $shipment->shipmentid }}" {{ old('shipmentid') == $shipment->shipmentid ? 'selected' : '' }}>
                                        {{ $shipment->shipment }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Payment Method <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="purchasepaymentmethodid" id="purchasepaymentmethodid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Payment Method</option>
                                @foreach($purchasePaymentMethods as $paymentMethod)
                                    <option value="{{ $paymentMethod->purchasepaymentmethodid }}" {{ old('purchasepaymentmethodid') == $paymentMethod->purchasepaymentmethodid ? 'selected' : '' }}>
                                        {{ $paymentMethod->paymentmethod }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Tax Payable <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="taxid" id="taxid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Tax</option>
                                @foreach($taxSettings as $tax)
                                    <option value="{{ $tax->taxid }}" {{ old('taxid') == $tax->taxid ? 'selected' : '' }}>
                                        {{ $tax->tax_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Purchase order measure <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="purchasemeasuresid" id="purchasemeasuresid" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Purchase Measuring</option>
                                @foreach($purchaseMeasures as $measure)
                                    <option value="{{ $measure->purchasemeasuresid }}" {{ old('purchasemeasuresid') == $measure->purchasemeasuresid ? 'selected' : '' }}>
                                        {{ $measure->purchasemeasure }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Product Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="product_name" id="product_name" 
                                   value="{{ old('product_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Product Price <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="product_price" id="product_price" 
                                   value="{{ old('product_price') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Product Notes
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="product_notes" id="product_notes" 
                                   value="{{ old('product_notes') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.products.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

