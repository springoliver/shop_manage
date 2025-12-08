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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Tables</span>
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

            <!-- Add New Table Form -->
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-file-text mr-2"></i> Add New Table
                </h2>
                
                <form action="{{ route('storeowner.possetting.update-floor-tables') }}" method="POST" id="myform1">
                    @csrf
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Pos Floor Sections <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="pos_floor_section_id" id="pos_floor_section_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Floor Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->pos_floor_section_id }}">
                                        {{ $section->pos_floor_section_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5"></label>
                        <div class="w-3/4 grid grid-cols-3 gap-4">
                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700">Table Number</p>
                                <input type="text" name="pos_floor_table_number" id="pos_floor_table_number" 
                                       value="{{ old('pos_floor_table_number') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700">Capacity</p>
                                <input type="text" name="pos_floor_table_seat" id="pos_floor_table_seat" 
                                       value="{{ old('pos_floor_table_seat') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                       required>
                            </div>
                            <div>
                                <p class="mb-2 text-sm font-medium text-gray-700">Colour</p>
                                <div class="flex items-center gap-2">
                                    <input type="color" name="pos_floor_table_colour" id="pos_floor_table_colour" 
                                           value="{{ old('pos_floor_table_colour', '#FFFFFF') }}"
                                           class="h-10 w-16 border border-gray-300 rounded cursor-pointer"
                                           onchange="updateTableColor(this.value)">
                                    <input type="text" id="pos_floor_table_colour_hex" 
                                           value="{{ old('pos_floor_table_colour', 'FFFFFF') }}"
                                           class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 uppercase"
                                           placeholder="FFFFFF" 
                                           maxlength="6"
                                           oninput="updateTableColorFromHex(this.value)">
                                </div>
                            </div>
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

            <!-- Tables List Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-800">Tables</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Table Number
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Capacity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Section
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer">
                                    Bg Colour
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @if($tables->count() > 0)
                                @foreach($tables as $table)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $table->pos_floor_table_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $table->pos_floor_table_seat }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $table->pos_floor_section_name ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded border border-gray-300" 
                                                     style="background-color: {{ $table->pos_floor_table_colour }}"></div>
                                                <span>{{ str_replace('#', '', $table->pos_floor_table_colour) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('storeowner.possetting.edit-table', base64_encode($table->pos_floor_table_id)) }}" 
                                               class="text-blue-600 hover:text-blue-800 mr-3" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="#" 
                                               onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this table?')) { document.getElementById('delete-form-{{ $table->pos_floor_table_id }}').submit(); }"
                                               class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <form id="delete-form-{{ $table->pos_floor_table_id }}" 
                                                  action="{{ route('storeowner.possetting.delete-table', base64_encode($table->pos_floor_table_id)) }}" 
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
                                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
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
        function updateTableColor(hexColor) {
            const hex = hexColor.replace('#', '');
            document.getElementById('pos_floor_table_colour_hex').value = hex.toUpperCase();
        }
        
        function updateTableColorFromHex(hexValue) {
            const hex = hexValue.replace('#', '').toUpperCase();
            if (/^[0-9A-F]{6}$/i.test(hex)) {
                document.getElementById('pos_floor_table_colour').value = '#' + hex;
            }
        }
    </script>
    @endpush
</x-storeowner-app-layout>

