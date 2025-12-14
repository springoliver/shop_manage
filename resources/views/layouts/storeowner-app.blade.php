<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Store App') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="//cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-sans antialiased">

    @php
    // Check both guards - employees can access storeowner routes with proper permissions
    $storeowner = Auth::guard('storeowner')->user();
    $employee = Auth::guard('employee')->user();
    $user = $storeowner ?? $employee;
    
    // Get stores - storeowners have multiple stores, employees have a single storeid
    if ($storeowner) {
        $stores = $storeowner->stores ?? collect();
    } else {
        // For employees, create a collection with their single store
        $storeid = $employee ? $employee->storeid : 0;
        $stores = $storeid ? collect([\App\Models\Store::find($storeid)])->filter() : collect();
    }
    
    // Build dynamic menu based on installed modules
    // Use employee menu service if employee is accessing, otherwise use storeowner menu service
    if ($employee) {
        $menuService = app(\App\Services\Employee\MenuService::class);
    } else {
        $menuService = app(\App\Services\StoreOwner\MenuService::class);
    }
    $navigation = $menuService->buildMenu();
    @endphp

    <div x-data="{ sidebarOpen: true }" class="flex min-h-screen bg-gray-200">
        <x-sidebar :menu-items="$navigation" />

        <div class="flex-1 flex flex-col h-screen overflow-y-auto">
            <x-header>
                 <div class="hidden sm:flex sm:items-center sm:ms-6">
                    @if($stores->count() > 1)
                        <form action="{{ route('storeowner.store.change') }}" method="POST" class="flex items-center space-x-2 mr-4">
                            @csrf
                            <select name="storeid" id="storeid" class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm" onchange="this.form.submit()">
                                @foreach($stores as $store)
                                    <option value="{{ $store->storeid }}" {{ session('storeid') == $store->storeid ? 'selected' : '' }}>
                                        {{ $store->store_name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ $user ? ($user->username ?? ($employee ? trim($employee->firstname . ' ' . $employee->lastname) : 'User')) : 'User' }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        @if($storeowner)
                            <x-dropdown-link :href="route('storeowner.profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('storeowner.logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('storeowner.logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @elseif($employee)
                            <!-- For employees, show employee profile link -->
                            <x-dropdown-link :href="route('employee.profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('employee.logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('employee.logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>
            </x-header>

            <main class="flex-1 p-6 space-y-6 md:p-8">

                <!-- Page Heading -->
                @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
                @endisset

                <!-- Page Content -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>

</html>

