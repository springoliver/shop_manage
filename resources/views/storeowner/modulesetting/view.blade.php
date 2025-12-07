<div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="edit_roster">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ $usergroup->first()->groupname ?? 'User Group' }}
                </h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mb-4">
                @if($usergroup->count() > 0)
                    <div class="mb-2">
                        <div class="grid grid-cols-2 gap-4 mb-2 pb-2 border-b border-gray-200">
                            <label class="text-sm font-medium text-gray-700">
                                <u>Module</u>
                            </label>
                            <label class="text-sm font-medium text-gray-700">
                                <u>Level</u>
                            </label>
                        </div>
                    </div>
                    @foreach($usergroup as $ug)
                        <div class="grid grid-cols-2 gap-4 mb-2 pb-2 border-b border-gray-200">
                            <div class="text-sm text-gray-700">
                                {{ $ug->module ?? 'N/A' }}:
                            </div>
                            <div class="text-sm text-gray-700">
                                {{ $ug->level ?? 'None' }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="mb-2">
                        <div class="grid grid-cols-2 gap-4 mb-2 pb-2 border-b border-gray-200">
                            <label class="text-sm font-medium text-gray-700">
                                <u>Module</u>
                            </label>
                            <label class="text-sm font-medium text-gray-700">
                                <u>Level</u>
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
    // Global function to close the modal and clear its content
    function closeModal() {
        var modalContentContainer = document.getElementById('modalcontent');
        if (modalContentContainer) {
            modalContentContainer.innerHTML = ''; // Clear the modal content
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(e) {
        var modal = document.getElementById('edit_roster');
        if (modal && e.target === modal) {
            closeModal();
        }
    });
</script>

