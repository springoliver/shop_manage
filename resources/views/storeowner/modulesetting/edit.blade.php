@section('page_header', 'Edit Module Setting')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.modulesetting.index') }}" class="ml-1 hover:text-gray-700">Module Setting</a>
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
        <h1 class="text-2xl font-semibold text-gray-800">{{ $title ?? 'Edit Module Setting' }}</h1>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <form action="{{ route('storeowner.modulesetting.update') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Module Access Levels -->
                <div class="space-y-4">
                    <div class="grid grid-cols-12 gap-4 pb-2 border-b border-gray-200">
                        <div class="col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Module</label>
                        </div>
                        <div class="col-span-9">
                            <label class="block text-sm font-medium text-gray-700">Level</label>
                        </div>
                    </div>

                    @if ($usergroup->count() > 0)
                        @foreach ($usergroup as $i => $ug)
                            <div class="grid grid-cols-12 gap-4 items-center">
                                <div class="col-span-3">
                                    <input type="hidden" name="hdnmodule{{ $i }}" value="{{ $ug->moduleid }}">
                                    <label class="block text-sm font-medium text-gray-700">{{ $ug->module }}</label>
                                </div>
                                <div class="col-span-9">
                                    <select name="accesslevel[]" id="accesslevel{{ $i }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring-gray-500">
                                        <option value="Admin" {{ ($ug->level ?? 'None') == 'Admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="View" {{ ($ug->level ?? 'None') == 'View' ? 'selected' : '' }}>View</option>
                                        <option value="None" {{ ($ug->level ?? 'None') == 'None' ? 'selected' : '' }}>None</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                        <input type="hidden" name="totalmodule" value="{{ $usergroup->count() }}">
                    @else
                        <div class="text-gray-500 text-center py-4">
                            No modules found for this user group.
                        </div>
                    @endif
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                    <input type="hidden" name="usergroupid" value="{{ base64_encode($usergroup->first()->usergroupid ?? '') }}">
                    <a href="{{ route('storeowner.modulesetting.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 transition">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-storeowner-app-layout>

