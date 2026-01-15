@section('page_header', 'Day Clock-In-Out')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Day Clock-In-Out</span>
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
                <span class="font-semibold">Day:</span> 
                <span>{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</span>
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
                <!-- Export route can be added later if needed -->
                <a href="#" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish(Roster)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish(Clock in-out App)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Break (Deducted)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total (Numeric)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
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
                                        {{ ucfirst($detail->firstname ?? '') }} {{ ucfirst($detail->lastname ?? '') }}
                                    </a>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->day ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->start_time ?? '0' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->end_time ?? '0' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->clockin ? \Carbon\Carbon::parse($detail->clockin)->format('Y-m-d H:i') : '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($detail->status) && $detail->status != 'clockout')
                                        {{ $detail->clockout ? \Carbon\Carbon::parse($detail->clockout)->format('Y-m-d H:i') : '' }}
                                    @else
                                        Still Working...
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if(isset($detail->status) && $detail->status != 'clockout' && isset($detail->totalBreakout))
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
                                    @if(isset($detail->status) && $detail->status != 'clockout' && isset($detail->total))
                                        @php
                                            $arrTotal = explode(".", number_format($detail->total, 2));
                                            $dec = isset($arrTotal[1]) ? $arrTotal[1] : 0;
                                        @endphp
                                        {{ $arrTotal[0] }}.{{ $dec }}
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
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-6 py-4 text-center text-gray-500">No clock in-out records found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if(count($clockDetails) > 0)
                <!-- Summary Section -->
                <div class="mt-4 p-4 bg-gray-50 border-t border-gray-200">
                    <div class="flex justify-between items-center">
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

    @push('scripts')
    <script>
        let clockInPicker, clockOutPicker;

        function editClockInOutData(eltid) {
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
                
                if (clockInPicker) {
                    clockInPicker.destroy();
                }
                if (clockOutPicker) {
                    clockOutPicker.destroy();
                }
                
                clockinInput.value = data.clockin || '';
                clockoutInput.value = data.clockout || '';
                document.getElementById('eltid').value = eltid;
                
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
                
                document.getElementById('editClockInOutModal').classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading clock in-out data');
            });
        }
        
        function closeEditModal() {
            document.getElementById('editClockInOutModal').classList.add('hidden');
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
