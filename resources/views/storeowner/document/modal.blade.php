<div class="flex items-center justify-between w-full mb-4 pb-3 border-b border-gray-200">
    <h4 class="text-lg font-semibold text-gray-900">Employee Document Detail</h4>
    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="document.getElementById('myModalImage').classList.add('hidden')" aria-label="Close">
        <i class="fas fa-times"></i>
    </button>
</div>
<div class="modal-body p-6">
    <form action="{{ route('storeowner.document.store') }}" method="POST" enctype="multipart/form-data" id="modalForm">
        @csrf
        <input type="hidden" name="employeeid" value="{{ $id }}" />
        
        @if($employee_document->count() > 0)
            <div class="mb-6">
                <h5 class="text-sm font-medium text-gray-700 mb-3">Existing Documents</h5>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    @foreach($employee_document as $doc)
                        <div class="border border-gray-200 rounded p-3">
                            <a href="{{ asset('storage/documents/' . $doc->docpath) }}" 
                               target="_blank" 
                               class="text-blue-600 hover:text-blue-800 text-sm block mb-2 truncate" title="{{ $doc->docname }}">
                                {{ $doc->docname }}
                            </a>
                            <a href="#" 
                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this document?')) { document.getElementById('delete-form-{{ $doc->docid }}').submit(); }"
                               class="text-red-600 hover:text-red-800 text-sm">
                                Remove
                            </a>
                            <form id="delete-form-{{ $doc->docid }}" 
                                  action="{{ route('storeowner.document.destroy', $doc->docid) }}" 
                                  method="POST" 
                                  style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="border-t border-gray-200 pt-6 mt-6">
            <h5 class="text-sm font-medium text-gray-700 mb-4">Add New Document</h5>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Document Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="docname" id="modalDocname" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       required>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Document <span class="text-red-500">*</span>
                </label>
                <input type="file" name="doc" id="modalDoc" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       required>
            </div>

            <div class="flex items-center justify-end gap-3">
                <button type="button" 
                        onclick="document.getElementById('myModalImage').classList.add('hidden')" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                    Upload Document
                </button>
            </div>
        </div>
    </form>
</div>

