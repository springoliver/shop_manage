@section('page_header', $userGroup->groupname)

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
                        <a href="{{ route('storeowner.usergroup.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">User Groups</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $userGroup->groupname }}</h1>
    </div>

    @if($modules->count() > 0)
        <form method="POST" action="{{ route('storeowner.usergroup.update', ['usergroup' => $userGroup->usergroupid]) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Hidden usergroupid field (base64 encoded like CI) -->
            <input type="hidden" name="usergroupid" value="{{ base64_encode($userGroup->usergroupid) }}" />

            <!-- Module Access Levels -->
            <div class="flex items-start gap-4">
                <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                    Module
                </label>
                <div class="w-3/4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                </div>
            </div>

            @foreach($modules as $index => $module)
                <div class="flex items-start gap-4">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                        <input type="hidden" name="hdnmodule{{ $index }}" value="{{ $module->moduleid }}" />
                        {{ $module->module ?? 'N/A' }}
                    </label>
                    <div class="w-3/4">
                        <select name="accesslevel[]" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="Admin" {{ ($module->level ?? 'None') == 'Admin' ? 'selected' : '' }}>Admin</option>
                            <option value="View" {{ ($module->level ?? 'None') == 'View' ? 'selected' : '' }}>View</option>
                            <option value="None" {{ ($module->level ?? 'None') == 'None' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                </div>
            @endforeach

            <input type="hidden" name="totalmodule" value="{{ $modules->count() }}" />

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('storeowner.usergroup.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Cancel
                </a>
                <x-primary-button>
                    Save
                </x-primary-button>
            </div>
        </form>
    @else
        <div class="mb-4 px-4 py-3 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded relative" role="alert">
            <span class="block sm:inline">All Modules are De-activated. Please install module.</span>
        </div>
    @endif
</x-storeowner-app-layout>

