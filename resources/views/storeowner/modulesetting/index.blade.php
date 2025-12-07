@section('page_header', 'Modules')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Modules</span>
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

    @if (!empty($cartModule) && count($cartModule) > 0)
        <div class="fixed right-5 top-20 z-50">
            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                <i class="fa fa-cart-arrow-down mr-2"></i>
                <span class="bg-green-500 text-black px-2 py-1 rounded ml-2">{{ count($cartModule) }}</span>
            </a>
        </div>
    @endif

    @php
        $module = [];
        $inst_module = [];
        $uninstall_module = [];
        $indate_diff = [];
        $undate_diff = [];
        
        foreach ($installModule as $im) {
            $module[] = $im['moduleid'];
        }
        
        $diffIndex = 0;
        foreach ($allModule as $i => $am) {
            if (in_array($am['moduleid'], $module)) {
                $inst_module[] = $am['moduleid'];
                if (!empty($diff[$diffIndex])) {
                    $indate_diff[] = $diff[$diffIndex];
                } else {
                    $indate_diff[] = '';
                }
                $diffIndex++;
            } else {
                $uninstall_module[] = $am['moduleid'];
                if (!empty($diff[$diffIndex])) {
                    $undate_diff[] = $diff[$diffIndex];
                } else {
                    $undate_diff[] = '';
                }
                $diffIndex++;
            }
        }
    @endphp

    <!-- Installed Modules Section -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="#" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition" title="Suggest a new module">
                Suggest a new module
            </a>
            
        </div>
        <h1 class="text-lg font-semibold text-gray-800 my-4">Installed Modules</h1>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            
            @if (count($installModule) > 0)
                @foreach ($installModule as $i => $im)
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow min-h-[185px] relative">
                        <a href="#" onclick="event.preventDefault(); window.openModuleInfoModal('module-info{{ $im['moduleid'] }}');" class="block">
                            <!-- Icon -->
                            <div class="absolute top-3 left-6 text-white text-3xl z-10">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-3 pl-20 min-h-[155px]">
                                <div class="flex items-start justify-between mb-2">
                                    <h5 class="text-white font-bold text-base uppercase flex-1">
                                        {{ $im['module'] }}
                                    </h5>
                                    <span class="bg-green-500 text-white px-2 py-1 rounded text-xs font-semibold ml-2 uppercase">
                                        Installed
                                    </span>
                                </div>
                                
                                <p class="text-white text-xl font-bold my-2">
                                    €{{ number_format($im['price_1months'], 2) }} 
                                    <small class="text-sm font-normal text-gray-300">per month</small>
                                </p>
                                
                                @if (isset($im['isTrial']) && $im['isTrial'] == 1)
                                    @php
                                        $remaintime = max(0, intval((strtotime($im['expire_date']) - time()) / (60 * 60 * 24)));
                                    @endphp
                                    <p class="text-green-400 text-sm mt-2" style="float: right;">
                                        {{ $remaintime }} days trial remain
                                    </p>
                                @endif
                            </div>
                            
                            <!-- Last Updated Footer -->
                            <div class="bg-gray-700 text-white text-sm px-6 py-2">
                                Last Updated: {{ !empty($indate_diff[$i]) ? $indate_diff[$i] : '0 days ago' }}
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- All Modules Section -->
    <div class="mb-8">
        <h1 class="text-lg font-semibold text-gray-800 mb-4">All Modules</h1>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach ($allModule as $i => $am)
                @if (in_array($am['moduleid'], $uninstall_module))
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow min-h-[185px] relative">
                        <!-- Icon -->
                        <div class="absolute top-3 left-6 text-white text-3xl z-10">
                            <i class="fas fa-thumbs-up"></i>
                        </div>
                        
                        <!-- Content -->
                        <div class="p-3 pl-20 min-h-[155px]">
                            <div class="flex items-start justify-between mb-2">
                                <h5 class="text-white font-bold text-base uppercase flex-1">
                                    {{ $am['module'] }}
                                </h5>
                                <span class="ml-2">
                                    @if (!in_array($am['moduleid'], $cartModule))
                                        <button type="button"
                                           title="Click to Install" 
                                           class="bg-yellow-500 text-black px-2 py-1 rounded text-xs font-semibold hover:bg-yellow-600 inline-block cursor-pointer border-0"
                                           onclick="openInstallModal('confirm-status{{ $am['moduleid'] }}'); return false;">
                                            Install Now
                                        </button>
                                    @else
                                        <span class="bg-green-500 text-black px-2 py-1 rounded text-xs font-semibold inline-block">
                                            In Cart
                                        </span>
                                    @endif
                                </span>
                            </div>
                            
                            <p class="text-white text-xl font-bold my-2">
                                €{{ number_format($am['price_1months'], 2) }} 
                                <small class="text-sm font-normal text-gray-300">per month</small>
                            </p>
                        </div>
                        
                        <!-- Last Updated Footer -->
                        <div class="bg-gray-700 text-white text-sm px-6 py-2">
                            Last Updated: {{ !empty($undate_diff[$i]) ? $undate_diff[$i] : '0 days ago' }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Modals for Install Confirmation -->
    @if ($storecount > 1)
        @foreach ($allModule as $am)
            @if (in_array($am['moduleid'], $uninstall_module))
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="confirm-status{{ $am['moduleid'] }}" style="display: none;" onclick="if(event.target === this) closeInstallModal('confirm-status{{ $am['moduleid'] }}')">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="relative bg-white rounded-lg shadow-lg p-5 w-full max-w-md" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Discount Offer</h3>
                                <button onclick="closeInstallModal('confirm-status{{ $am['moduleid'] }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form action="{{ route('storeowner.modulesetting.install') }}" method="post">
                                @csrf
                                <div class="mb-4">
                                    Do you Want to take Discount? <br/>
                                    <div class="mt-2">
                                        <label class="inline-flex items-center mr-4">
                                            <input type="radio" name="status" value="Yes" class="form-radio">
                                            <span class="ml-2">Yes</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="status" value="No" checked class="form-radio">
                                            <span class="ml-2">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end space-x-4">
                                    <input type="hidden" name="moduleid" value="{{ base64_encode($am['moduleid']) }}">
                                    <button type="button" onclick="closeInstallModal('confirm-status{{ $am['moduleid'] }}')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @else
        @foreach ($allModule as $am)
            @if (in_array($am['moduleid'], $uninstall_module))
                <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="confirm-status{{ $am['moduleid'] }}" style="display: none;" onclick="if(event.target === this) closeInstallModal('confirm-status{{ $am['moduleid'] }}')">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="relative bg-white rounded-lg shadow-lg p-5 w-full max-w-md" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Install</h3>
                                <button onclick="closeInstallModal('confirm-status{{ $am['moduleid'] }}')" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <form action="{{ route('storeowner.modulesetting.install') }}" method="post">
                                @csrf
                                <div class="mb-4">
                                    Are you Sure You Want to Install? <br/>
                                    <div class="mt-2">
                                        <label class="inline-flex items-center mr-4">
                                            <input type="radio" name="install" value="Yes" class="form-radio">
                                            <span class="ml-2">Yes</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="radio" name="install" value="No" checked class="form-radio">
                                            <span class="ml-2">No</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="flex items-center justify-end space-x-4">
                                    <input type="hidden" name="moduleid" value="{{ base64_encode($am['moduleid']) }}">
                                    <button type="button" onclick="closeInstallModal('confirm-status{{ $am['moduleid'] }}')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    <!-- Modals for Module Info -->
    @foreach ($installModule as $im)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden" id="module-info{{ $im['moduleid'] }}" style="display: none;" onclick="if(event.target === this) window.closeModuleInfoModal('module-info{{ $im['moduleid'] }}')">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="relative bg-white rounded-lg shadow-lg p-6 w-full max-w-lg" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $im['module'] }}</h3>
                        <button onclick="window.closeModuleInfoModal('module-info{{ $im['moduleid'] }}')" class="text-gray-400 hover:text-gray-600 text-xl">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mb-4 text-gray-700 leading-relaxed [&_p]:mb-2 [&_p]:last:mb-0">
                        {!! $im['module_detailed_info'] ?? $im['module_description'] ?? 'No description available.' !!}
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Modal Content Container for AJAX -->
    <div id="modalcontent"></div>

    <script>
        // Make functions globally accessible
        window.openInstallModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                document.body.style.overflow = 'hidden';
                console.log('Modal opened:', modal);
            } else {
                console.error('Modal not found with ID:', id);
                console.log('Available modals:', document.querySelectorAll('[id^="confirm-status"]'));
            }
        };

        window.closeInstallModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };

        window.openModuleInfoModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
                modal.style.alignItems = 'center';
                modal.style.justifyContent = 'center';
                document.body.style.overflow = 'hidden';
                console.log('Module info modal opened:', modal);
            } else {
                console.error('Module info modal not found:', id);
            }
        };

        window.closeModuleInfoModal = function(id) {
            let modal = document.getElementById(id);
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };

        // Close modal when clicking outside (on backdrop)
        document.addEventListener('click', function (e) {
            // Check if click is on modal backdrop (the gray overlay)
            if (e.target.id && (e.target.id.startsWith('confirm-status') || e.target.id.startsWith('module-info'))) {
                // Only close if clicking directly on the backdrop, not on modal content
                if (e.target === document.getElementById(e.target.id)) {
                    if (e.target.id.startsWith('confirm-status')) {
                        window.closeInstallModal(e.target.id);
                    } else if (e.target.id.startsWith('module-info')) {
                        window.closeModuleInfoModal(e.target.id);
                    }
                }
            }
        });

        function get_moduleview(usergroupid) {
            fetch('{{ route('storeowner.modulesetting.view') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ usergroupid: usergroupid })
            })
            .then(response => response.text())
            .then(data => {
                var modalContent = document.getElementById('modalcontent');
                modalContent.innerHTML = data;
                // Show the modal
                var modal = document.getElementById('edit_roster');
                if (modal) {
                    modal.classList.remove('hidden');
                }
            });
        }
    </script>
</x-storeowner-app-layout>
