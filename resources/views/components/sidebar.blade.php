@props(['menuItems' => [] , 'activeRoute' => null])

@php
    use Illuminate\Support\Facades\Route;
    
    // Determine active route if not provided
    $activeRoute = $activeRoute ?? (request()->route() ? request()->route()->getName() : null);
    
    // Helper function to check if a route or its submenu is active
    $isActive = function($item) use ($activeRoute) {
        if (isset($item['route']) && $activeRoute === $item['route']) {
            return true;
        }
        if (isset($item['submenu'])) {
            foreach ($item['submenu'] as $subitem) {
                if (isset($subitem['route']) && $activeRoute === $subitem['route']) {
                    return true;
                }
            }
        }
        return false;
    };
    
    // Helper function to check if submenu should be open
    $isSubmenuOpen = function($item) use ($activeRoute) {
        if (isset($item['submenu'])) {
            foreach ($item['submenu'] as $subitem) {
                if (isset($subitem['route']) && $activeRoute === $subitem['route']) {
                    return true;
                }
            }
        }
        return false;
    };
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
            @foreach ($menuItems as $index => $item)
                @if(isset($item['type']) && $item['type'] === 'submenu' && isset($item['submenu']))
                    @php
                        $itemLabel = $item['label'] ?? '';
                        $submenuId = 'submenu-' . $index;
                        $shouldBeOpen = $isSubmenuOpen($item);
                    @endphp
                    {{-- Submenu Item --}}
                    <div>
                        <button
                            type="button"
                            onclick="toggleSubmenu('{{ $submenuId }}', this)"
                            class="w-full flex items-center justify-between pl-4 py-2 rounded-md text-gray-400 {{ $isActive($item) ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}"
                        >
                            <div class="flex items-center">
                                {!! $item['icon'] !!}
                                <span class="ml-3" x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                            </div>
                            <i class="fa fa-chevron-right ml-auto transition-transform duration-200 submenu-chevron" 
                               x-show="sidebarOpen" 
                               x-transition></i>
                        </button>
                        
                        {{-- Submenu Items --}}
                        @if(count($item['submenu']) > 0)
                            <div id="{{ $submenuId }}" 
                                 class="ml-4 mt-1 space-y-1 submenu-content {{ $shouldBeOpen ? '' : 'hidden' }}"
                                 style="display: {{ $shouldBeOpen ? 'block' : 'none' }};">
                                @foreach ($item['submenu'] as $subitem)
                                    @php
                                        $routeExists = false;
                                        $routeUrl = '#';
                                        if (isset($subitem['route']) && $subitem['enabled']) {
                                            if (Route::has($subitem['route'])) {
                                                $routeUrl = route($subitem['route']);
                                                $routeExists = true;
                                            }
                                        }
                                    @endphp
                                    @if($routeExists || !isset($subitem['route']))
                                        <a
                                            href="{{ $routeUrl }}"
                                            class="flex items-center px-4 py-2 rounded-md text-sm text-gray-400 {{ isset($subitem['route']) && $activeRoute === $subitem['route'] ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}"
                                        >
                                            {!! $subitem['icon'] !!}
                                            <span class="ml-3">{{ $subitem['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Regular Link Item --}}
                    @php
                        $routeExists = false;
                        $routeUrl = '#';
                        if (isset($item['route']) && $item['enabled']) {
                            if (Route::has($item['route'])) {
                                $routeUrl = route($item['route']);
                                $routeExists = true;
                            }
                        }
                    @endphp
                    @if($routeExists || !isset($item['route']))
                        <a
                            href="{{ $routeUrl }}"
                            class="flex items-center px-4 py-2 rounded-md text-gray-400 {{ isset($item['route']) && $activeRoute === $item['route'] ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 hover:text-white' }}"
                        >
                            {!! $item['icon'] !!}
                            <span class="ml-3" x-show="sidebarOpen" x-transition>{{ $item['label'] }}</span>
                        </a>
                    @endif
                @endif
            @endforeach

        </nav>
    </div>
</aside>

<script>
function toggleSubmenu(submenuId, button) {
    // Get the submenu content element
    const submenuContent = document.getElementById(submenuId);
    if (!submenuContent) return;
    
    // Get all submenu content elements
    const allSubmenus = document.querySelectorAll('.submenu-content');
    
    // Get the chevron icon for this button
    const chevron = button.querySelector('.submenu-chevron');
    
    // Check if this submenu is currently open
    const isOpen = !submenuContent.classList.contains('hidden');
    
    // Close all submenus first
    allSubmenus.forEach(submenu => {
        submenu.classList.add('hidden');
        submenu.style.display = 'none';
    });
    
    // Remove rotate class from all chevrons
    document.querySelectorAll('.submenu-chevron').forEach(icon => {
        icon.classList.remove('rotate-90');
    });
    
    // If this submenu was closed, open it
    if (!isOpen) {
        submenuContent.classList.remove('hidden');
        submenuContent.style.display = 'block';
        if (chevron) {
            chevron.classList.add('rotate-90');
        }
    }
}
</script>
