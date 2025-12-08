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
                        <a href="{{ route('storeowner.possetting.index') }}" class="ml-1 hover:text-gray-700">POS</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.possetting.index') }}" class="ml-1 hover:text-gray-700">Tables</a>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 bg-green-100 border border-green-400 text-green-700 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-700 rounded relative" role="alert">
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

            <!-- Edit Table Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-align-justify mr-2"></i> Edit Tables
                </h2>
                
                <form action="{{ route('storeowner.possetting.update-floor-tables') }}" method="POST" id="myform">
                    @csrf
                    <input type="hidden" name="pos_floor_table_id" value="{{ base64_encode($table->pos_floor_table_id) }}">
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <select name="pos_floor_section_id" id="pos_floor_section_id" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                                <option value="">Select Section</option>
                                @foreach($sections as $section)
                                    <option value="{{ $section->pos_floor_section_id }}" 
                                            {{ $table->pos_floor_section_id == $section->pos_floor_section_id ? 'selected' : '' }}>
                                        {{ $section->pos_floor_section_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Table Number <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_floor_table_number" id="pos_floor_table_number" 
                                   value="{{ old('pos_floor_table_number', $table->pos_floor_table_number) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Seating Capacity <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_floor_table_seat" id="pos_floor_table_seat" 
                                   value="{{ old('pos_floor_table_seat', $table->pos_floor_table_seat) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Table Colour <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <div class="flex items-center gap-2">
                                <input type="color" name="pos_floor_table_colour" id="pos_floor_table_colour" 
                                       value="{{ $table->pos_floor_table_colour }}"
                                       class="h-10 w-16 border border-gray-300 rounded cursor-pointer"
                                       onchange="updateTableColor(this.value)">
                                <input type="text" id="pos_floor_table_colour_hex" 
                                       value="{{ str_replace('#', '', $table->pos_floor_table_colour) }}"
                                       class="flex-1 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 uppercase"
                                       placeholder="FFFFFF" 
                                       maxlength="6"
                                       oninput="updateTableColorFromHex(this.value)">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.possetting.tables') }}" 
                           class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                            Cancel
                        </a>
                    </div>
                </form>
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

