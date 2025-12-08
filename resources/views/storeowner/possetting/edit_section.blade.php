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
                        <a href="{{ route('storeowner.possetting.index') }}" class="ml-1 hover:text-gray-700">Sections</a>
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

            <!-- Edit Section Form -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">
                    <i class="fas fa-align-justify mr-2"></i> Edit Sections
                </h2>
                
                <form action="{{ route('storeowner.possetting.update-floor-sections') }}" method="POST" id="myform" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pos_floor_section_id" value="{{ base64_encode($section->pos_floor_section_id) }}">
                    
                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section Name <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_floor_section_name" id="pos_floor_section_name" 
                                   value="{{ old('pos_floor_section_name', $section->pos_floor_section_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section Colour <span class="text-red-500">*</span>
                        </label>
                        <div class="flex w-3/4">
                            <input type="color" name="pos_floor_section_colour" id="pos_floor_section_colour" 
                                   value="{{ $section->pos_floor_section_colour }}"
                                   class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                   onchange="updateColor(this.value)">
                            <input type="text" id="pos_floor_section_colour_hex" 
                                   value="{{ str_replace('#', '', $section->pos_floor_section_colour) }}"
                                   class="ml-2 px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 uppercase"
                                   placeholder="FFFFFF" 
                                   maxlength="6"
                                   oninput="updateColorFromHex(this.value)">
                        </div>
                    </div>

                    <div class="flex items-start gap-4 mb-6">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">
                            Section Listing <span class="text-red-500">*</span>
                        </label>
                        <div class="w-3/4">
                            <input type="text" name="pos_section_list_number" id="pos_section_list_number" 
                                   value="{{ old('pos_section_list_number', $section->pos_section_list_number) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   required>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <button type="submit" class="px-6 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Save
                        </button>
                        <a href="{{ route('storeowner.possetting.sections') }}" 
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
        function updateColor(hexColor) {
            const hex = hexColor.replace('#', '');
            document.getElementById('pos_floor_section_colour_hex').value = hex.toUpperCase();
        }
        
        function updateColorFromHex(hexValue) {
            const hex = hexValue.replace('#', '').toUpperCase();
            if (/^[0-9A-F]{6}$/i.test(hex)) {
                document.getElementById('pos_floor_section_colour').value = '#' + hex;
            }
        }
    </script>
    @endpush
</x-storeowner-app-layout>

