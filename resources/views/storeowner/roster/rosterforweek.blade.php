@section('page_header', 'Search & Edit Roster')

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
                        <a href="{{ route('storeowner.roster.index') }}" class="ml-1 hover:text-gray-700">Roster</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Search & Edit</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Navigation Buttons -->
    <div class="mb-2 flex flex-wrap gap-2">
        <a href="{{ route('storeowner.roster.index') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Roster Template
        </a>
        <a href="{{ route('storeowner.roster.viewweekroster') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Current Roster
        </a>
        <form action="{{ route('storeowner.roster.searchweekroster') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="dateofbirth" value="{{ date('Y-m-d') }}">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                Search & Edit
            </button>
        </form>
        <a href="javascript:void(0);" onclick="document.getElementById('searchPrintForm').submit();" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
            Search & Print
        </a>
        <form id="searchPrintForm" action="{{ route('storeowner.roster.searchprintroster') }}" method="POST" class="hidden">
            @csrf
            <input type="hidden" name="dateofbirth" value="{{ date('Y-m-d') }}">
        </form>
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

    <!-- Search Form -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">
                        Roster for week: {{ $weeknumber ?? '' }} {{ isset($weeknumber) ? '-' : '' }} {{ $year ?? date('Y') }}
                    </h2>
                </div>
                <form action="{{ route('storeowner.roster.searchweekroster') }}" method="POST" class="flex items-center space-x-2" id="weekRosterSearchForm">
                    @csrf
                    <label for="dateofbirth" class="text-sm font-medium text-gray-700">Select Week:</label>
                    <input type="date" name="dateofbirth" id="dateofbirth" 
                           value="{{ isset($dateInput) && $dateInput ? (strpos($dateInput, '-') === 2 ? \Carbon\Carbon::createFromFormat('d-m-Y', $dateInput)->format('Y-m-d') : $dateInput) : date('Y-m-d') }}"
                           class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 text-sm" 
                           required>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                        Search
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Display Roster Table -->
    @if(isset($rostersByEmployee) && $rostersByEmployee->count() > 0)
        <div class="bg-white rounded-lg shadow">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Employee Roster</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee Name</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sunday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tuesday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wednesday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thursday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Friday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saturday</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Hours</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php
                                $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            @endphp
                            @if(isset($employees) && count($employees) > 0)
                                @foreach($employees as $employee)
                                    @php
                                        $employeeRosters = $weekRosters->where('employeeid', $employee->employeeid);
                                        $rosterByDay = [];
                                        foreach($employeeRosters as $roster) {
                                            $rosterByDay[$roster->day] = $roster;
                                        }
                                    @endphp
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                            {{ $employee->firstname }} {{ $employee->lastname }}
                                        </td>
                                        @foreach($days as $day)
                                            <td class="px-4 py-3 text-sm text-gray-900">
                                                @if(isset($rosterByDay[$day]) && $rosterByDay[$day]->start_time != '00:00:00')
                                                    @php
                                                        $r = $rosterByDay[$day];
                                                        $ts1 = strtotime($r->end_time);
                                                        $ts2 = strtotime($r->start_time);
                                                        $diff = ceil(abs($ts1 - $ts2) / 3600);
                                                        $color = $diff <= 6 ? 'green' : 'red';
                                                    @endphp
                                                    <div>{{ date('H:i', strtotime($r->start_time)) }} to {{ date('H:i', strtotime($r->end_time)) }}</div>
                                                    <div class="text-{{ $color }}-600 text-xs">
                                                        {{ $diff }}Hrs
                                                        @if($diff > 6)
                                                            <span class="text-red-600">(Long Shift)</span>
                                                        @endif
                                                    </div>
                                                @else
                                                    OFF
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="px-4 py-3 text-sm text-green-600 font-medium">
                                            <span class="text-green-600">{{ $totalHours[$employee->employeeid] ?? 0 }} Hours</span>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            <a href="javascript:void(0);" onclick="getEditEmployeeRoster({{ $employee->employeeid }})" 
                                               class="text-blue-600 hover:text-blue-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @endif


    @push('scripts')
    <script>
        // Auto-submit search form after successful save to refresh table (matching CI behavior)
        // CI redirects back to the same page which refreshes the table
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                // If we have a success message and the date input exists, auto-submit the search form
                const dateInput = document.getElementById('dateofbirth');
                const searchForm = document.getElementById('weekRosterSearchForm');
                
                if (dateInput && searchForm && dateInput.value) {
                    // Small delay to ensure page is fully loaded
                    setTimeout(function() {
                        searchForm.submit();
                    }, 100);
                }
            });
        @endif
        
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        function compare(temp) {
            return temp.replace(':', '.');
        }
        
        function checkWorkingHour(obj) {
            const empid = document.getElementById('employeeid_select').value;
            if (!empid || empid === '0') {
                days.forEach(day => {
                    document.getElementById(day + '_start').value = 'off';
                    document.getElementById(day + '_end').value = 'off';
                });
                return false;
            }
            
            const employeeId = empid.split('||')[0];
            const rosterDayHrs = parseInt(document.getElementById('roster_day_hrs_' + employeeId).value) || 0;
            const rosterWeekHrs = parseInt(document.getElementById('roster_week_hrs_' + employeeId).value) || 0;
            
            const dayName = obj.id.replace('_start', '').replace('_end', '');
            const startSelect = document.getElementById(dayName + '_start');
            const endSelect = document.getElementById(dayName + '_end');
            
            if (startSelect.value !== 'off' && endSelect.value !== 'off') {
                const startTime = compare(startSelect.value);
                const endTime = compare(endSelect.value);
                
                if (parseFloat(endTime) <= parseFloat(startTime)) {
                    alert('Please select valid Start/End Time of ' + dayName);
                    endSelect.value = 'off';
                    return false;
                }
                
                // Calculate hours for this day
                const start = new Date('2000-01-01 ' + startSelect.value + ':00').getTime();
                const end = new Date('2000-01-01 ' + endSelect.value + ':00').getTime();
                const diffHours = (end - start) / (1000 * 60 * 60);
                
                if (rosterDayHrs > 0 && rosterDayHrs < diffHours) {
                    alert('Maximum roster day hours for this employee is over.');
                    obj.value = 'off';
                    return false;
                }
            }
            
            // Calculate total week hours
            let totalWeekHours = 0;
            days.forEach(day => {
                const start = document.getElementById(day + '_start').value;
                const end = document.getElementById(day + '_end').value;
                if (start !== 'off' && end !== 'off') {
                    const startTime = new Date('2000-01-01 ' + start + ':00').getTime();
                    const endTime = new Date('2000-01-01 ' + end + ':00').getTime();
                    totalWeekHours += (endTime - startTime) / (1000 * 60 * 60);
                }
            });
            
            if (rosterWeekHrs > 0 && rosterWeekHrs < totalWeekHours) {
                alert('Maximum roster week hours for this employee is over.');
                obj.value = 'off';
                return false;
            }
            
            // Sync start/end changes
            if (startSelect.value === 'off') {
                endSelect.value = 'off';
            }
            if (endSelect.value === 'off') {
                startSelect.value = 'off';
            }
        }
        
        // Sync start/end time changes on blur
        days.forEach(day => {
            const startSelect = document.getElementById(day + '_start');
            const endSelect = document.getElementById(day + '_end');
            
            if (startSelect) {
                startSelect.addEventListener('blur', function() {
                    if (this.value === 'off') {
                        endSelect.value = 'off';
                    }
                });
            }
            
            if (endSelect) {
                endSelect.addEventListener('blur', function() {
                    if (this.value === 'off') {
                        startSelect.value = 'off';
                    }
                });
            }
        });
        
        function getEditEmployeeRoster(employeeid) {
            const weeknumber = {{ $weeknumber ?? 0 }};
            const weekid = {{ $weekid ?? 0 }};
            const dateInput = document.getElementById('dateofbirth') ? document.getElementById('dateofbirth').value : '{{ $dateInput ?? date('Y-m-d') }}';
            const formData = new FormData();
            formData.append('employeeid', employeeid);
            formData.append('weeknumber', weeknumber);
            formData.append('weekid', weekid);
            formData.append('dateInput', dateInput);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch("{{ route('storeowner.ajax.get-edit-employee-roster') }}", {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(response => response.text())
            .then(data => {
                // Remove any existing modal first
                const existingModal = document.getElementById('editRosterModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Remove any existing backdrop
                const existingBackdrop = document.querySelector('.modal-backdrop');
                if (existingBackdrop) {
                    existingBackdrop.remove();
                }
                
                // Create a container div and append modal HTML to body
                const tempContainer = document.createElement('div');
                tempContainer.innerHTML = data;
                
                // Extract and execute scripts from the loaded HTML
                // Scripts in innerHTML don't execute automatically
                const scripts = tempContainer.querySelectorAll('script');
                scripts.forEach(oldScript => {
                    const newScript = document.createElement('script');
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent;
                    }
                    document.body.appendChild(newScript);
                    oldScript.remove();
                });
                
                document.body.appendChild(tempContainer);
                
                // Get the modal element
                const modalElement = document.getElementById('editRosterModal');
                if (modalElement) {
                    // Remove aria-hidden to fix accessibility warning
                    modalElement.removeAttribute('aria-hidden');
                    
                    // Show modal by adding classes
                    modalElement.classList.remove('hidden');
                    modalElement.classList.add('show');
                    modalElement.style.display = 'block';
                    
                    // Add backdrop
                    const backdrop = document.createElement('div');
                    backdrop.className = 'fixed inset-0 bg-black bg-opacity-50 z-40';
                    backdrop.id = 'modalBackdrop';
                    document.body.appendChild(backdrop);
                    
                    // Prevent body scroll
                    document.body.style.overflow = 'hidden';
                    
                    // Ensure checkWorkingHourModel function is available globally
                    // The function should be defined in the modal script, but ensure it's accessible
                    if (typeof window.checkWorkingHourModel === 'undefined') {
                        console.warn('checkWorkingHourModel function not found, modal script may not have loaded');
                    }
                    
                    // Close modal handlers
                    const closeModal = function() {
                        modalElement.classList.remove('show');
                        modalElement.classList.add('hidden');
                        modalElement.style.display = 'none';
                        // Restore aria-hidden when closing
                        modalElement.setAttribute('aria-hidden', 'true');
                        if (backdrop && backdrop.parentNode) {
                            backdrop.remove();
                        }
                        document.body.style.overflow = '';
                        tempContainer.remove();
                    };
                    
                    // Close button handlers
                    const closeButtons = modalElement.querySelectorAll('[data-bs-dismiss="modal"], .close');
                    closeButtons.forEach(btn => {
                        btn.addEventListener('click', closeModal);
                    });
                    
                    // Close on backdrop click
                    backdrop.addEventListener('click', closeModal);
                    
                    // Close on Escape key
                    const escapeHandler = function(e) {
                        if (e.key === 'Escape') {
                            closeModal();
                            document.removeEventListener('keydown', escapeHandler);
                        }
                    };
                    document.addEventListener('keydown', escapeHandler);
                }
            })
            .catch(error => {
                console.error('Error loading roster edit form:', error);
                alert('Error loading roster edit form. Please try again.');
            });
        }
    </script>
    @endpush
</x-storeowner-app-layout>
