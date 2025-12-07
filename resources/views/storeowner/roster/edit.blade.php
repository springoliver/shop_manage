@section('page_header', 'Edit Roster')

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
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('storeowner.roster.index') }}" class="ml-1 hover:text-gray-700">Rosters Template</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Roster - {{ $employee->firstname }} {{ $employee->lastname }}</h1>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('storeowner.roster.update', base64_encode($employee->employeeid)) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Start Time</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">End Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
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
                                @php
                                    $roster = $rosters[$day] ?? null;
                                    $startTime = $roster && $roster->start_time != '00:00:00' ? $roster->start_time : 'off';
                                    $endTime = $roster && $roster->end_time != '00:00:00' ? $roster->end_time : 'off';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $day }}</td>
                                    <td class="px-4 py-3">
                                        <select name="{{ $day }}_start" id="{{ $day }}_start" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off" {{ $startTime == 'off' ? 'selected' : '' }}>Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}" {{ $startTime == $time ? 'selected' : '' }}>{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="{{ $day }}_end" id="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHour(this)">
                                            <option value="off" {{ $endTime == 'off' ? 'selected' : '' }}>Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}" {{ $endTime == $time ? 'selected' : '' }}>{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <a href="{{ route('storeowner.roster.index') }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const rosterDayHrs = {{ $employee->roster_day_hrs }};
        const rosterWeekHrs = {{ $employee->roster_week_hrs }};

        days.forEach(day => {
            const startSelect = document.getElementById(day + '_start');
            const endSelect = document.getElementById(day + '_end');
            
            startSelect.addEventListener('blur', function() {
                if (this.value === 'off') {
                    endSelect.value = 'off';
                }
            });
            
            endSelect.addEventListener('blur', function() {
                if (this.value === 'off') {
                    startSelect.value = 'off';
                }
            });
        });

        function checkWorkingHour(obj) {
            const dayName = obj.id.replace('_start', '').replace('_end', '');
            const startSelect = document.getElementById(dayName + '_start');
            const endSelect = document.getElementById(dayName + '_end');

            if (startSelect.value !== 'off' && endSelect.value !== 'off') {
                if (endSelect.value <= startSelect.value) {
                    alert('Please select valid Start/End Time of ' + dayName);
                    endSelect.value = 'off';
                    return false;
                }

                const start = new Date('2000-01-01 ' + startSelect.value);
                const end = new Date('2000-01-01 ' + endSelect.value);
                const diffHours = (end - start) / (1000 * 60 * 60);

                if (rosterDayHrs < diffHours) {
                    alert('Maximum roster day hours for this employee is over.');
                    obj.value = 'off';
                    return false;
                }
            }

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
    </script>
    @endpush
</x-storeowner-app-layout>

