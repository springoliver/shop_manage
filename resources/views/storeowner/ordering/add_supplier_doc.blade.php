@section('page_header', 'Add Supplier Document')
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
                        <a href="{{ route('storeowner.ordering.index_supplier_doc') }}" class="ml-1 hover:text-gray-700">Document</a>
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

            @if ($errors->any())
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-align-justify mr-2"></i> Add Supplier Document
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('storeowner.ordering.update_supplier_doc') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          id="myform" 
                          name="myform">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Supplier <span class="text-red-500">*</span>
                            </label>
                            <select name="supplierid" 
                                    id="supplierid" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                    required>
                                <option value="">Select Supplier</option>
                                @foreach($storeSuppliers as $supplier)
                                    <option value="{{ $supplier->supplierid }}" {{ old('supplierid') == $supplier->supplierid ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Document Type <span class="text-red-500">*</span>
                            </label>
                            <select name="docs_type_id" 
                                    id="docs_type_id" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                    required>
                                <option value="">Select Document Type</option>
                                @foreach($supplierDocTypes as $docType)
                                    <option value="{{ $docType->docs_type_id }}" {{ old('docs_type_id') == $docType->docs_type_id ? 'selected' : '' }}>
                                        {{ $docType->docs_type_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Document Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="docname" 
                                   id="docname"
                                   value="{{ old('docname') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                   required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Document Date-Month <span class="text-red-500">*</span>
                            </label>
                            <input type="date" 
                                   name="doc_date" 
                                   id="doc_date"
                                   value="{{ old('doc_date') }}"
                                   class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                   required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Document <span class="text-red-500">*</span>
                            </label>
                            <input type="file" 
                                   name="doc" 
                                   id="doc"
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500"
                                   required>
                            <p class="mt-1 text-sm text-gray-500">Accepted formats: PDF, DOC, DOCX, XLS, XLSX, JPG, JPEG, PNG (Max: 10MB)</p>
                        </div>
                        
                        <div class="flex space-x-3 mt-6">
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Save
                            </button>
                            <a href="{{ route('storeowner.ordering.index_supplier_doc') }}" 
                               class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('myform');
            const supplierSelect = document.getElementById('supplierid');
            const docTypeSelect = document.getElementById('docs_type_id');
            const docNameInput = document.getElementById('docname');
            const docDateInput = document.getElementById('doc_date');
            const docFileInput = document.getElementById('doc');

            // Set custom validation messages
            supplierSelect.addEventListener('invalid', function(e) {
                e.preventDefault();
                if (!this.value) {
                    this.setCustomValidity('Please select supplier');
                }
            });

            supplierSelect.addEventListener('change', function() {
                this.setCustomValidity('');
            });

            docTypeSelect.addEventListener('invalid', function(e) {
                e.preventDefault();
                if (!this.value) {
                    this.setCustomValidity('Please select document type');
                }
            });

            docTypeSelect.addEventListener('change', function() {
                this.setCustomValidity('');
            });

            docNameInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                if (!this.value.trim()) {
                    this.setCustomValidity('Please enter Document name');
                }
            });

            docNameInput.addEventListener('input', function() {
                this.setCustomValidity('');
            });

            docDateInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                if (!this.value) {
                    this.setCustomValidity('Please select date');
                }
            });

            docDateInput.addEventListener('change', function() {
                this.setCustomValidity('');
            });

            docFileInput.addEventListener('invalid', function(e) {
                e.preventDefault();
                if (!this.files || this.files.length === 0) {
                    this.setCustomValidity('Please select document file');
                }
            });

            docFileInput.addEventListener('change', function() {
                this.setCustomValidity('');
            });

            // Form submission
            form.addEventListener('submit', function(e) {
                // Check file size (10MB = 10 * 1024 * 1024 bytes)
                if (docFileInput.files && docFileInput.files[0]) {
                    const fileSize = docFileInput.files[0].size;
                    const maxSize = 10 * 1024 * 1024; // 10MB
                    
                    if (fileSize > maxSize) {
                        e.preventDefault();
                        docFileInput.setCustomValidity('File size must be less than 10MB');
                        docFileInput.reportValidity();
                        return false;
                    }
                }

                // If all validations pass, form will submit
                if (!form.checkValidity()) {
                    e.preventDefault();
                    form.reportValidity();
                    return false;
                }
            });
        });
    </script>
    @endpush
</x-storeowner-app-layout>

