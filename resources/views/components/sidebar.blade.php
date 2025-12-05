@props(['menuItems' => [] , 'activeRoute' => null])

@php
    // Determine active route if not provided
    $activeRoute = $activeRoute ?? request()->route()->getName();
@endphp
<aside
    class="flex-shrink-0 w-64 bg-gray-800 border-r border-gray-700 transition-all duration-300"
    :class="{ 'w-64': sidebarOpen, 'w-20': !sidebarOpen }"
>
    <div class="flex flex-col h-full">

        <div class="flex items-center justify-between flex-shrink-0 h-16 px-4 bg-gray-900">

            <a href="/" class="flex items-center text-white" x-show="sidebarOpen" x-transition>
                <x-application-logo class="w-8 h-8" stroke="currentColor" fill="#ffffff" />

                <span class="ml-2 text-xl font-semibold" x-show="sidebarOpen" x-transition>
                    {{ config('app.name', 'Store App') }}
                </span>
            </a>

            {{-- Hamburger Toggle Button --}}
            <button @click="sidebarOpen = !sidebarOpen" class="text-gray-400 rounded-md hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                <span class="sr-only">Toggle sidebar</span>
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-2 py-4 space-y-2 overflow-y-auto">

            {{-- Loop over the $menuItems prop --}}
            @foreach ($menuItems as $item)
                <a
                    href="{{ $item['enabled'] ? route($item['route']) : route('admin.dashboard') }}"
                    class="flex items-center px-4 py-2 rounded-md text-gray-400 {{ $activeRoute === $item['route'] ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}"
                >
                    {!! $item['icon'] !!}
                    <span class="ml-3" x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                </a>
            @endforeach

        </nav>
    </div>
</aside>
