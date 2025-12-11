@section('page_header', 'Week Clock-In-Out (All Employees)')

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
                        <a href="{{ route('storeowner.clocktime.index') }}" class="ml-1 hover:text-gray-700">Clock-in-out</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Week Clock-In-Out (All Employees)</span>
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

    <!-- Header Info -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center mb-4">
            <a href="javascript:history.back()" class="text-blue-600 hover:text-blue-800">
                &lt;&lt; Back
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
            <div>
                <span class="font-semibold">Start Date:</span> 
                <span>{{ \Carbon\Carbon::parse($startDate)->format('d-m-Y') }}</span>
            </div>
            <div>
                <span class="font-semibold">End Date:</span> 
                <span>{{ \Carbon\Carbon::parse($endDate)->format('d-m-Y') }}</span>
            </div>
            @if(count($clockDetails) > 0)
                <div>
                    <span class="font-semibold">Week:</span> 
                    <span>{{ $weekNumber }}-{{ $year }}</span>
                </div>
            @endif
        </div>
        <div class="mt-4 flex justify-between gap-2">
            @if(count($clockDetails) > 0)
                @php
                    $firstDetail = $clockDetails->first();
                    $exportDate = \Carbon\Carbon::parse($firstDetail->clockin)->format('Y-m-d');
                @endphp
                <a href="{{ route('storeowner.clocktime.export-week-allemp', [
                    'weekid' => $calculatedWeekid,
                    'date' => $exportDate
                ]) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                    Export Clock Time
                </a>
            @endif
            <a href="{{ route('storeowner.clocktime.compare_weekly_hrs') }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                Weekly Hours Analysis
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start<br/>(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish<br/>(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start<br/>(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish<br/>(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Break<br/>(Deducted)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total<br/>(Numeric)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Edit</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($clockDetails ?? [] as $detail)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('d-m-Y') : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('storeowner.clocktime.week-clock-time', [
                                        'employeeid' => base64_encode($detail->employeeid),
                                        'date' => \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d')
                                    ]) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ ucfirst($detail->employee->firstname ?? '') }} {{ ucfirst($detail->employee->lastname ?? '') }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->day }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->roster_start_time ?? '00:00' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->roster_end_time ?? '00:00' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->clockin)
                                        @php
                                            $rosterStart = \Carbon\Carbon::parse($detail->roster_start_time ?? '00:00:00');
                                            $clockIn = \Carbon\Carbon::parse($detail->clockin);
                                        @endphp
                                        @if($detail->roster_start_time && $detail->roster_start_time !== '0' && $rosterStart->lt($clockIn))
                                            <span class="text-red-600">{{ \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d H:i') }}</span>
                                        @else
                                            <span class="text-green-600">{{ \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d H:i') }}</span>
                                        @endif
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->status != 'clockout')
                                        {{ $detail->clockout ? \Carbon\Carbon::parse($detail->clockout)->format('Y-m-d H:i') : '' }}
                                    @else
                                        Still Working...
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->status != 'clockout' && isset($detail->totalBreakout))
                                        @php
                                            $breakHours = floor($detail->totalBreakout / 60);
                                            $breakMinutes = $detail->totalBreakout % 60;
                                        @endphp
                                        {{ $breakHours }} hr {{ $breakMinutes }} min
                                    @else
                                        Still Working...
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($detail->status != 'clockout' && isset($detail->total))
                                        {{ number_format($detail->total, 2) }}
                                    @else
                                        Still Working...
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="#" 
                                           onclick="event.preventDefault(); editClockInOutData({{ $detail->eltid }});" 
                                           class="text-gray-600 hover:text-gray-900" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('storeowner.clocktime.delete-shift', base64_encode($detail->eltid)) }}" 
                                           onclick="return confirm('Are you sure you want to delete this shift?')" 
                                           class="text-red-600 hover:text-red-900" 
                                           title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->editby ?? '' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center text-gray-500">No clock in-out records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($clockDetails) > 0)
                <!-- Summary Section with Buttons -->
                <div class="mt-4 p-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <a href="{{ route('storeowner.clocktime.generate-week-payslip') }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm inline-block">
                                Generate payslip for all employees for week: {{ $weekNumber }}-{{ $year }}
                            </a>
                        </div>
                        
                        <div class="text-right">
                            <span class="font-semibold text-sm">Total Payroll Hour (Numeric):</span>
                            @php
                                $pdata = explode(".", number_format($totalPayrol, 2));
                                $hours = isset($pdata[0]) ? $pdata[0] : 0;
                                $minutes = isset($pdata[1]) ? $pdata[1] : '00';
                            @endphp
                            <span class="text-sm">{{ $hours }}.{{ $minutes }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Button -->
                <div class="flex justify-between mt-4">
                    <div>
                        <form action="{{ route('storeowner.clocktime.upload-allemployee-daily-hours') }}" method="POST" id="uploadHoursForm">
                            @csrf
                            @foreach($clockDetails as $detail)
                                <input type="hidden" name="eltid[]" value="{{ $detail->eltid }}">
                                <input type="hidden" name="employeeid[]" value="{{ $detail->employeeid }}">
                                <input type="hidden" name="storeid[]" value="{{ $detail->storeid }}">
                                <input type="hidden" name="weekno[]" value="{{ $weekNumber }}">
                                <input type="hidden" name="week_start[]" value="{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}">
                                <input type="hidden" name="week_end[]" value="{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}">
                                <input type="hidden" name="shiftdate[]" value="{{ \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d') }}">
                                <input type="hidden" name="shiftday[]" value="{{ $detail->day }}">
                                <input type="hidden" name="clockins[]" value="{{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d H:i') : '' }}">
                                <input type="hidden" name="clockouts[]" value="{{ ($detail->status != 'clockout' && $detail->clockout) ? \Carbon\Carbon::parse($detail->clockout)->format('Y-m-d H:i') : '' }}">
                                <input type="hidden" name="break_deducted[]" value="{{ ($detail->status != 'clockout' && isset($detail->totalBreakout)) ? floor($detail->totalBreakout / 60) . ' hr ' . ($detail->totalBreakout % 60) . ' min' : '' }}">
                                <input type="hidden" name="total_hours[]" value="{{ ($detail->status != 'clockout' && isset($detail->total)) ? number_format($detail->total, 2) : '' }}">
                            @endforeach
                            <input type="hidden" name="year" value="{{ $year }}">
                            <input type="hidden" name="hours_worked" value="{{ number_format($totalPayrol, 2) }}">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                                Upload payroll hours for dashboard: {{ $weekNumber }}-{{ $year }}
                            </button>
                        </form>
                    </div>
                    <div class="mt-4 text-right">
                        <a href="{{ route('storeowner.clocktime.week-allemp-payroll-hrs', [
                            'weekid' => $calculatedWeekid,
                            'date' => \Carbon\Carbon::parse($startDate)->format('Y-m-d')
                        ]) }}" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
                            All employee total payroll hours for week: {{ $weekNumber }}-{{ $year }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Edit Clock In-Out Modal -->
    <div id="editClockInOutModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Employee Clock In-out</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form id="editClockInOutForm" method="POST" action="{{ route('storeowner.clocktime.edit-emp-timecard') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ClockIn</label>
                            <input type="text" 
                                   name="clockin" 
                                   id="clockin" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   placeholder="YYYY-MM-DD HH:MM:SS"
                                   required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">ClockOut</label>
                            <input type="text" 
                                   name="clockout" 
                                   id="clockout" 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" 
                                   placeholder="YYYY-MM-DD HH:MM:SS"
                                   required>
                            <input type="hidden" name="eltid" id="eltid">
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" 
                                onclick="closeEditModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400">
                            Close
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        // Initialize Flatpickr for Edit Modal (will be reinitialized when modal opens)
        let clockInPicker, clockOutPicker;

        function editClockInOutData(eltid) {
            // Fetch clock in-out data via AJAX
            fetch('{{ route("storeowner.clocktime.edit-clock-inout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ eltid: eltid })
            })
            .then(response => response.json())
            .then(data => {
                const clockinInput = document.getElementById('clockin');
                const clockoutInput = document.getElementById('clockout');
                
                // Destroy existing pickers if they exist
                if (clockInPicker) {
                    clockInPicker.destroy();
                }
                if (clockOutPicker) {
                    clockOutPicker.destroy();
                }
                
                // Set values
                clockinInput.value = data.clockin || '';
                clockoutInput.value = data.clockout || '';
                document.getElementById('eltid').value = eltid;
                
                // Initialize Flatpickr for edit modal (datetime format: YYYY-MM-DD HH:MM:SS)
                clockInPicker = flatpickr('#clockin', {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i:s',
                    time_24hr: true,
                    allowInput: true
                });
                
                clockOutPicker = flatpickr('#clockout', {
                    enableTime: true,
                    dateFormat: 'Y-m-d H:i:s',
                    time_24hr: true,
                    allowInput: true
                });
                
                // Show modal
                document.getElementById('editClockInOutModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading clock in-out data');
            });
        }
        
        function closeEditModal() {
            document.getElementById('editClockInOutModal').classList.add('hidden');
            // Destroy pickers when closing modal
            if (clockInPicker) {
                clockInPicker.destroy();
                clockInPicker = null;
            }
            if (clockOutPicker) {
                clockOutPicker.destroy();
                clockOutPicker = null;
            }
        }
    </script>
    @endpush
</x-storeowner-app-layout>

