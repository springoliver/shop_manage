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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Floor Layout</span>
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

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Navigation Tabs -->
            @include('storeowner.possetting._navigation')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Current Sections -->
                <div class="bg-white rounded-lg shadow p-6">
                    @if($sections->count() <= 0)
                        <div class="mb-4 p-4 bg-yellow-100 border border-yellow-400 text-yellow-700 rounded">
                            <h4 class="font-semibold">Sections not set yet!</h4>
                        </div>
                    @else
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">Current Sections</h4>
                        <div class="flex flex-wrap gap-3">
                            <a href="{{ route('storeowner.possetting.floor-layout') }}" 
                               class="px-4 py-2 {{ !request()->routeIs('storeowner.possetting.floor-layout-section') ? 'bg-blue-600' : 'bg-blue-500' }} text-white rounded-md hover:bg-blue-700">
                                All
                            </a>
                            @foreach($sections as $section)
                                @php
                                    $isActive = request()->routeIs('storeowner.possetting.floor-layout-section') && 
                                                request()->route('pos_floor_section_id') == base64_encode($section->pos_floor_section_id);
                                @endphp
                                <a href="{{ route('storeowner.possetting.floor-layout-section', base64_encode($section->pos_floor_section_id)) }}" 
                                   class="px-4 py-2 {{ $isActive ? 'bg-blue-600' : 'bg-blue-500' }} text-white rounded-md hover:bg-blue-700"
                                   title="{{ $section->pos_floor_section_name }}">
                                    {{ $section->pos_floor_section_name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- All Tables -->
                <div class="bg-white rounded-lg shadow p-6">
                    @if($sections->count() > 0)
                        <h4 class="text-lg font-semibold text-gray-800 mb-4">All Tables</h4>
                        @if($tables->count() > 0)
                            <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                                @foreach($tables as $table)
                                    <div class="text-center">
                                        <table class="w-full border border-gray-300">
                                            <tbody>
                                                <tr>
                                                    <td class="p-1 border-b border-gray-200"></td>
                                                </tr>
                                                <tr>
                                                    <td class="p-3 text-center border-b border-gray-200" 
                                                        style="background-color: {{ $table->pos_floor_table_colour }}; min-height: 60px;"
                                                        title="Table {{ $table->pos_floor_table_number }}">
                                                        <a href="{{ route('storeowner.possetting.edit-table', base64_encode($table->pos_floor_table_id)) }}" 
                                                           class="text-gray-900 hover:text-blue-600 font-semibold cursor-pointer block">
                                                            <strong>{{ $table->pos_floor_table_number }}</strong>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="p-1"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-center py-8">No tables found for this section.</p>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

