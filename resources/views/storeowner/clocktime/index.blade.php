@section('page_header', 'Clock-In-Out')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Clock-in-out</span>
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

    <!-- Search Form -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <form action="{{ route('storeowner.clocktime.clockreport') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div class="block text-sm font-medium text-gray-700 mb-2">Select Date:</div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="date" id="date" value="{{ $searchDate ?? '' }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       placeholder="dd-mm-yyyy" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="date_end" id="date_end" value="{{ $searchDateEnd ?? '' }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       placeholder="dd-mm-yyyy" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select id="employeeid" name="employeeid[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="" {{ (!isset($selectedEmployeeIds) || empty($selectedEmployeeIds)) ? 'selected' : '' }}>All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->employeeid }}" 
                                {{ (isset($selectedEmployeeIds) && in_array($employee->employeeid, $selectedEmployeeIds)) ? 'selected' : '' }}>
                            {{ $employee->firstname }} {{ $employee->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Search
                </button>
            </div>
        </form>
    </div>

    <!-- Export PDF Form -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <form action="{{ route('storeowner.clocktime.exportpdf') }}" method="POST" class="flex flex-wrap items-end gap-4">
            @csrf
            <div class="block text-sm font-medium text-gray-700 mb-2">Select Date:</div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="date" id="date2" value="{{ $searchDate ?? '' }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       placeholder="dd-mm-yyyy" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="date_end" id="date_end2" value="{{ $searchDateEnd ?? '' }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500" 
                       placeholder="dd-mm-yyyy" required>
            </div>
            <div class="flex-1 min-w-[200px]">
                <select id="employeeid2" name="employeeid[]" multiple class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
                    <option value="" selected>All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->employeeid }}">
                            {{ $employee->firstname }} {{ $employee->lastname }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                    Export PDF
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Week</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Roster Hrs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clock in-out App Hrs</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clockDetails ?? [] as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($detail->employee && $detail->clockin)
                                        <a href="{{ route('storeowner.clocktime.week-clock-time', [
                                            'employeeid' => base64_encode($detail->employeeid),
                                            'date' => \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d')
                                        ]) }}" class="text-blue-600 hover:text-blue-800" title="Click here to manage clock in-out of {{ ucfirst($detail->employee->firstname ?? '') }} {{ ucfirst($detail->employee->lastname ?? '') }}">
                                            {{ ucfirst($detail->employee->firstname ?? '') }} {{ ucfirst($detail->employee->lastname ?? '') }}
                                        </a>
                                    @else
                                        {{ ucfirst($detail->employee->firstname ?? '') }} {{ ucfirst($detail->employee->lastname ?? '') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->week && $detail->clockin)
                                        <a href="{{ route('storeowner.clocktime.week-clock-time-allemp', [
                                            'weekid' => base64_encode($detail->weekid),
                                            'date' => \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d')
                                        ]) }}" class="text-blue-600 hover:text-blue-800" title="Week">
                                            {{ $detail->week->weeknumber ?? '' }}-{{ \Carbon\Carbon::parse($detail->clockin)->format('Y') }}
                                        </a>
                                    @else
                                        {{ $detail->week->weeknumber ?? '' }}-{{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('Y') : '' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('d-m-Y') : '' }}
                                    <br/>
                                    ({{ $detail->day }})
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->roster_start_time ?? '00:00' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->roster_end_time ?? '00:00' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d H:i') : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->status == 'clockout')
                                        Still Working...
                                    @else
                                        {{ $detail->clockout ? \Carbon\Carbon::parse($detail->clockout)->format('Y-m-d H:i') : '' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @php
                                        // Match CI's calculation: abs($to_time - $from_time) / 60
                                        // CI uses strtotime on time-only strings, which uses today's date
                                        $rosterStartTime = $detail->roster_start_time ?? '00:00';
                                        $rosterEndTime = $detail->roster_end_time ?? '00:00';
                                        
                                        // Ensure H:i:s format for strtotime
                                        if (strlen($rosterStartTime) == 5) {
                                            $rosterStartTime .= ':00';
                                        }
                                        if (strlen($rosterEndTime) == 5) {
                                            $rosterEndTime .= ':00';
                                        }
                                        
                                        // Use strtotime like CI does (matching CI line 167-169)
                                        $to_time = strtotime($rosterStartTime);
                                        $from_time = strtotime($rosterEndTime);
                                        $total_roster_minute = round(abs($to_time - $from_time) / 60, 2);
                                        
                                        $hours = floor($total_roster_minute / 60);
                                        $minutes = $total_roster_minute % 60;
                                    @endphp
                                    {{ $hours }} Hour {{ $minutes }} Minute
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->status != 'clockout' && isset($detail->timediff))
                                        @php
                                            $clockHours = floor($detail->timediff / 60);
                                            $clockMinutes = $detail->timediff % 60;
                                        @endphp
                                        {{ $clockHours }} hours {{ $clockMinutes }} minutes
                                    @else
                                        ---
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 text-center text-gray-500">No clock in-out records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Wait for jQuery and Vite bundle to be fully loaded
        (function() {
            var retries = 0;
            var maxRetries = 50; // 5 seconds max wait (50 * 100ms)
            
            function initClockTime() {
                // Check if jQuery is available
                var $ = window.jQuery || window.$;
                
                if (!$ || typeof $ !== 'function') {
                    retries++;
                    if (retries < maxRetries) {
                        setTimeout(initClockTime, 100);
                        return;
                    } else {
                        console.error('jQuery failed to load after ' + maxRetries + ' retries');
                        return;
                    }
                }
                
                // Ensure DOM is ready
                $(function() {
                    // Date pickers
                    if (typeof flatpickr !== 'undefined') {
                        flatpickr('#date, #date2', {
                            dateFormat: 'd-m-Y',
                            maxDate: 'today'
                        });
                        
                        flatpickr('#date_end, #date_end2', {
                            dateFormat: 'd-m-Y',
                            maxDate: 'today'
                        });
                    }

                    // Select2 for employee dropdowns - match CI's simple initialization
                    if (typeof $.fn.select2 !== 'undefined') {
                        $('#employeeid').select2();
                        $('#employeeid2').select2();
                        
                        // Handle exclusive selection: "All Employees" OR individual employees (not both)
                        $('#employeeid, #employeeid2').on('select2:select', function (e) {
                            var data = e.params.data;
                            var $select = $(this);
                            
                            // Use setTimeout to ensure Select2 has updated its internal state
                            setTimeout(function() {
                                var selectedValues = $select.val() || [];
                                
                                if (data.id === '') {
                                    // "All Employees" was just selected - clear all other selections
                                    $select.val(['']).trigger('change');
                                } else {
                                    // An individual employee was just selected - remove "All Employees" if present
                                    if (selectedValues.indexOf('') !== -1) {
                                        selectedValues = selectedValues.filter(function(val) {
                                            return val !== '';
                                        });
                                        $select.val(selectedValues).trigger('change');
                                    }
                                }
                            }, 100);
                        });
                    } else {
                        console.warn('Select2 plugin not found. Dropdowns will use native select.');
                    }
                });
            }
            
            // Start initialization when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initClockTime);
            } else {
                initClockTime();
            }
        })();
    </script>
    @endpush
</x-storeowner-app-layout>

