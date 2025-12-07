@section('page_header', 'Rosters Template')

<x-storeowner-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('storeowner.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Dashboard
                    </a>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Rosters Template</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header with Add Week Roster Button -->
    <div class="flex justify-between items-center mb-6"> 
        <!-- Department Filter Buttons -->
        @if($departments->count() > 0)
            <div class="mb-4 flex flex-wrap gap-2">
                @foreach($departments as $dept)
                    <a href="{{ route('storeowner.roster.index-dept', base64_encode($dept->departmentid)) }}" 
                    class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        {{ $dept->department }} Roster
                    </a>
                @endforeach
            </div>
        @endif

        <div class="flex space-x-3">
            <a href="{{ route('storeowner.roster.weekroster') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition">
                <i class="fas fa-plus mr-2"></i>
                Add Week Roster
            </a>
        </div>
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

    <!-- Add Roster Form -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Roster</h2>
            <form action="{{ route('storeowner.roster.store') }}" method="POST" id="rosterForm">
                @csrf
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sun</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mon</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tue</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wed</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thu</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fri</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sat</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3">
                                    <select id="employeeid_select" name="employeeid" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                        <option value="0">Please Select</option>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->employeeid }}">{{ $emp->firstname }} {{ $emp->lastname }}</option>
                                            <input type="hidden" id="roster_week_hrs_{{ $emp->employeeid }}" value="{{ $emp->roster_week_hrs }}" />
                                            <input type="hidden" id="roster_day_hrs_{{ $emp->employeeid }}" value="{{ $emp->roster_day_hrs }}" />
                                        @endforeach
                                    </select>
                                </td>
                                @php
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                    $timeOptions = [];
                                    $start = strtotime('10:30');
                                    $end = strtotime('23:30');
                                    $current = $start;
                                    while ($current <= $end) {
                                        $timeOptions[] = date('H:i:s', $current);
                                        $current = strtotime('+30 minutes', $current);
                                    }
                                @endphp
                                @foreach($days as $day)
                                    <td class="px-4 py-3">
                                        <select id="{{ $day }}_start" name="{{ $day }}_start" class="w-full mb-1 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off">Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}">{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                        <select id="{{ $day }}_end" name="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off">Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}">{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                @endforeach
                                <td class="px-4 py-3">
                                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Save
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    <!-- Existing Rosters Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Existing Rosters</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sun</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mon</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tue</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wed</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thu</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fri</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sat</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($employeedata as $employee)
                            @php
                                $employeeRosters = $weekroster->where('employeeid', $employee->employeeid);
                                $totalHours = 0;
                                $rosterByDay = [];
                                foreach($employeeRosters as $roster) {
                                    $rosterByDay[$roster->day] = $roster;
                                    if ($roster->start_time != '00:00:00') {
                                        $diff = (strtotime($roster->end_time) - strtotime($roster->start_time)) / 3600;
                                        $totalHours += ceil($diff);
                                    }
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
                                                $diff = (strtotime($r->end_time) - strtotime($r->start_time)) / 3600;
                                                $color = $diff <= 6 ? 'green' : 'red';
                                            @endphp
                                            <div>{{ date('H:i', strtotime($r->start_time)) }} to {{ date('H:i', strtotime($r->end_time)) }}</div>
                                            <div class="text-{{ $color }}-600 text-xs">{{ ceil($diff) }}Hrs</div>
                                        @else
                                            Off
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-4 py-3 text-sm text-green-600 font-medium">{{ $totalHours }} Hours</td>
                                <td class="px-4 py-3 text-sm">
                                    <div class="flex space-x-2">
                                        <a href="javascript:void(0);" onclick="getRosterData({{ $employee->employeeid }})" class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('storeowner.roster.destroy', base64_encode($employee->employeeid)) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this roster?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        <a href="{{ route('storeowner.roster.view', base64_encode($employee->employeeid)) }}" class="text-green-600 hover:text-green-800" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-6 text-center text-gray-500">No rosters found</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($weekroster->count() > 0)
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="8" class="px-4 py-3 text-right text-sm font-medium text-gray-700">Total:</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    @php
                                        $grandTotal = 0;
                                        foreach($weekroster as $r) {
                                            if ($r->start_time != '00:00:00') {
                                                $diff = (strtotime($r->end_time) - strtotime($r->start_time)) / 3600;
                                                $grandTotal += ceil($diff);
                                            }
                                        }
                                    @endphp
                                    {{ $grandTotal }} Hours
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Edit Roster Modal -->
    <div id="editRosterModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div id="modalContent"></div>
        </div>
    </div>

    @push('scripts')
    <script>
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        // Sync start/end time changes
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

        function checkWorkingHour(obj) {
            const empid = document.getElementById('employeeid_select').value;
            if (empid == '0') {
                alert('Please select Employee');
                days.forEach(day => {
                    document.getElementById(day + '_start').value = 'off';
                    document.getElementById(day + '_end').value = 'off';
                });
                return false;
            }

            const rosterDayHrs = parseInt(document.getElementById('roster_day_hrs_' + empid).value);
            const rosterWeekHrs = parseInt(document.getElementById('roster_week_hrs_' + empid).value);

            const dayName = obj.id.replace('_start', '').replace('_end', '');
            const startSelect = document.getElementById(dayName + '_start');
            const endSelect = document.getElementById(dayName + '_end');

            if (startSelect.value !== 'off' && endSelect.value !== 'off') {
                if (endSelect.value <= startSelect.value) {
                    alert('Please select valid Start/End Time of ' + dayName);
                    endSelect.value = 'off';
                    return false;
                }

                // Calculate hours
                const start = new Date('2000-01-01 ' + startSelect.value);
                const end = new Date('2000-01-01 ' + endSelect.value);
                const diffHours = (end - start) / (1000 * 60 * 60);

                if (rosterDayHrs < diffHours) {
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
                    const startTime = new Date('2000-01-01 ' + start);
                    const endTime = new Date('2000-01-01 ' + end);
                    totalWeekHours += (endTime - startTime) / (1000 * 60 * 60);
                }
            });

            if (rosterWeekHrs < totalWeekHours) {
                alert('Maximum roster week hours for this employee is over.');
                obj.value = 'off';
                return false;
            }
        }

        function getRosterData(employeeid) {
            // This will be implemented when we create the edit view
            window.location.href = '{{ route("storeowner.roster.edit", ":id") }}'.replace(':id', btoa(employeeid));
        }
    </script>
    @endpush
</x-storeowner-app-layout>

