<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS - {{ config('app.name', 'Store App') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    @php
        $user = Auth::guard('employee')->user();
    @endphp

    <!-- Header -->
    <header class="bg-gray-800 text-white shadow">
        <div class="flex justify-between items-center px-6 py-4">
            <div class="text-xl font-bold">{{ config('app.name', 'MaxiManage') }}</div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <button class="flex items-center gap-2 hover:text-gray-300">
                        <span>{{ $user->username ?? 'Employee' }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('employee.logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded transition">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex h-screen" style="height: calc(100vh - 64px);">
        <!-- Left Sidebar - Product Categories (POS Menu) -->
        <aside class="w-64 bg-gray-200 border-r border-gray-300 overflow-y-auto p-4">
            <style>
                .small-box {
                    font-size: 17px;
                    color: #000000;
                    border-radius: 5px;
                    display: block;
                    margin-bottom: 8px;
                    min-height: 40px;
                    width: 100%;
                    font-weight: bold;
                    text-transform: uppercase;
                    padding: 10px;
                    border: none;
                    cursor: pointer;
                    transition: background-color 0.2s;
                }
                .bg-aqua {
                    background-color: #cccccc !important;
                }
                .bg-aqua:hover {
                    background-color: #b3b3b3 !important;
                }
            </style>

            @forelse ($catalogProductCategories as $category)
                <button type="button" class="small-box bg-aqua w-full text-left mb-2">
                    {{ $category['catalog_product_category_name'] }}
                </button>
            @empty
                <p class="text-gray-500 text-sm">No product categories found.</p>
            @endforelse
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 bg-gray-100 overflow-y-auto p-4">
            <style>
                .pep {
                    background: blue;
                    height: 55px;
                    width: 55px;
                    box-sizing: border-box;
                    display: inline-block;
                    margin: 5px;
                    position: relative;
                }
                .pep input {
                    width: 100%;
                    height: 100%;
                    outline: none;
                    border: 0px;
                    background: transparent;
                    font-size: 16px;
                    text-align: center;
                    color: white;
                    text-transform: uppercase;
                    cursor: pointer;
                    background: rgba(255, 255, 255, 0.3);
                }
                .pep input::-webkit-input-placeholder {
                    color: rgba(255, 255, 255, 0.7);
                }
                .pep input:focus {
                    background: transparent;
                }
            </style>

            <div class="container mx-auto mt-8">
                <!-- Table Grid - 4 rows x 5 columns (matching CI screenshot) -->
                <div>
                    @for ($row = 0; $row < 4; $row++)
                        <div class="flex justify-start mb-2">
                            @for ($col = 0; $col < 5; $col++)
                                <div class="pep">
                                    <input maxlength="3" 
                                           name="table_{{ $row }}_{{ $col }}" 
                                           id="table_{{ $row }}_{{ $col }}" 
                                           placeholder="?" 
                                           type="text">
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
        </main>
    </div>
</body>
</html>
