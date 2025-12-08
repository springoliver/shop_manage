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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">POS</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Sections</span>
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
            <!-- Navigation Tabs -->
            @include('storeowner.possetting._navigation')

            <!-- Add New Section Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-file-text mr-2"></i> Add New Section
                </h2>
                
                @if($sections->count() <= 0)
                    <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                        <h4 class="font-semibold">Sections not set yet!</h4>
                    </div>
                @endif
                
                <form action="{{ route('storeowner.possetting.update-floor-sections') }}" method="POST" id="myform" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_floor_section_name" id="pos_floor_section_name" 
                                   value="{{ old('pos_floor_section_name') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Colour <span class="text-red-500">*</span>
                        </label>
                        <div class="flex w-3/4">
                            <input type="color" name="pos_floor_section_colour" id="pos_floor_section_colour" 
                                   value="{{ old('pos_floor_section_colour', '#FFFFFF') }}"
                                   class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                   onchange="updateColor(this.value)">
                            <input type="text" id="pos_floor_section_colour_hex" 
                                   value="{{ old('pos_floor_section_colour', 'FFFFFF') }}"
                                   class="ml-2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 uppercase"
                                   placeholder="FFFFFF" 
                                   maxlength="6"
                                   oninput="updateColorFromHex(this.value)">
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section Listing Number <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_section_list_number" id="pos_section_list_number" 
                                   value="{{ old('pos_section_list_number') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Floor Table Layout Image
                        </label>
                        <div class="w-3/4">
                            <div class="relative">
                                <input type="file" 
                                       name="floor_layout_img" 
                                       id="floor_layout_img" 
                                       accept=".jpg,.jpeg,.png,image/jpeg,image/jpg,image/png"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-gray-50 file:text-gray-700
                                              hover:file:bg-gray-100
                                              border border-gray-300 rounded-md
                                              focus:outline-none focus:ring-2 focus:ring-gray-500">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">
                                Select .jpg, .jpeg and .png file size up to 2MB only. Minimum dimension: 1000x700
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Add
                        </button>
                        <a href="{{ route('storeowner.possetting.index') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>

            <!-- Sections List Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Sections</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Section List Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Section Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Section Colour
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($sections->count() > 0)
                                @foreach($sections as $section)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $section->pos_section_list_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $section->pos_floor_section_name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded border border-gray-300" 
                                                     style="background-color: {{ $section->pos_floor_section_colour }}"></div>
                                                <span>{{ str_replace('#', '', $section->pos_floor_section_colour) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('storeowner.possetting.edit-section', base64_encode($section->pos_floor_section_id)) }}" 
                                               class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this section?')) { document.getElementById('delete-form-{{ $section->pos_floor_section_id }}').submit(); }"
                                               class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $section->pos_floor_section_id }}" 
                                                  action="{{ route('storeowner.possetting.delete-section', base64_encode($section->pos_floor_section_id)) }}" 
                                                  method="POST" 
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No records found.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateColor(hexColor) {
            // Remove # if present
            const hex = hexColor.replace('#', '');
            document.getElementById('pos_floor_section_colour_hex').value = hex.toUpperCase();
        }
        
        function updateColorFromHex(hexValue) {
            // Remove # if present
            const hex = hexValue.replace('#', '').toUpperCase();
            if (/^[0-9A-F]{6}$/i.test(hex)) {
                document.getElementById('pos_floor_section_colour').value = '#' + hex;
            }
        }
    </script>
    @endpush
</x-storeowner-app-layout>

