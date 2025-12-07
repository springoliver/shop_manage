<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="edit_roster">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $groupname ?? 'User Group' }}
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                @if($modules->count() > 0)
                    <div class="mb-2">
                        <div class="flex items-center gap-4 mb-2">
                            <label class="w-1/2 text-sm font-medium text-red-600">
                                <u>Module</u>
                            </label>
                            <label class="w-1/2 text-sm font-medium text-red-600">
                                <u>Access level</u>
                            </label>
                        </div>
                    </div>
                    @foreach($modules as $module)
                        <div class="flex items-center gap-4 mb-2 pb-2 border-b border-gray-200">
                            <div class="w-1/2 text-sm text-gray-700">
                                {{ $module->module ?? 'N/A' }}:
                            </div>
                            <div class="w-1/2 text-sm text-gray-700">
                                {{ $module->level ?? 'None' }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="mb-2">
                        <div class="flex items-center gap-4 mb-2">
                            <label class="w-1/2 text-sm font-medium text-red-600">
                                <u>Module</u>
                            </label>
                            <label class="w-1/2 text-sm font-medium text-red-600">
                                <u>Access level</u>
                            </label>
                        </div>
                    </div>
                    <div class="text-sm text-gray-700">
                        No Module Found
                    </div>
                @endif
            </div>
            <div class="flex items-center justify-end">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function closeModal() {
        var modal = document.getElementById('edit_roster');
        if (modal) {
            modal.remove();
        }
        // Also try to remove from modalcontent container
        var modalContent = document.getElementById('modalcontent');
        if (modalContent) {
            modalContent.innerHTML = '';
        }
    }

    // Close modal on outside click - need to wait for element to exist
    setTimeout(function() {
        var modal = document.getElementById('edit_roster');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal();
                }
            });
        }
    }, 100);
</script>

