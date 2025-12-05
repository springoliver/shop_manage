<header class="flex items-center justify-between flex-shrink-0 h-16 px-6 bg-white border-b">
    <!-- Logo -->
    <div class="shrink-0 flex items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @yield('page_header', 'Dashboard')
        </h2>
    </div>
    {{ $slot }}
</header>
