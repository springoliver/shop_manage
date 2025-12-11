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
                        <a href="{{ route('storeowner.suppliers.index') }}" class="ml-1 hover:text-gray-700">Suppliers</a>
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
                    <i class="fas fa-file-text-o mr-2"></i> Suppliers
                </h2>
                
                <form action="{{ route('storeowner.suppliers.update') }}" method="POST" id="myform">
                    @csrf
                    
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
                            Purchase Order Supplier <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <label class="inline-flex items-center mr-4">
                                <input type="radio" name="purchase_supplier" value="Yes" {{ old('purchase_supplier', 'Yes') == 'Yes' ? 'checked' : '' }} class="mr-2" required>
                                <span>Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="purchase_supplier" value="No" {{ old('purchase_supplier') == 'No' ? 'checked' : '' }} class="mr-2" required>
                                <span>No</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Supplier Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="supplier_name" id="supplier_name" 
                                   value="{{ old('supplier_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Supplier Phone <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="supplier_phone" id="supplier_phone" 
                                   value="{{ old('supplier_phone') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Supplier Mobile <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="supplier_phone2" id="supplier_phone2" 
                                   value="{{ old('supplier_phone2') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Supplier Email <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="email" name="supplier_email" id="supplier_email" 
                                   value="{{ old('supplier_email') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Supplier Representative <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="supplier_rep" id="supplier_rep" 
                                   value="{{ old('supplier_rep') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.suppliers.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

