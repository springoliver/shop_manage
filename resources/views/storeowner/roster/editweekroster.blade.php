@section('page_header', 'Edit Week Roster')

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
                        <a href="{{ route('storeowner.roster.index') }}" class="ml-1 hover:text-gray-700">Roster</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Edit Week Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Edit Week Roster - {{ $employee->firstname }} {{ $employee->lastname }}</h1>
    </div>

    <!-- Week Info -->
    @if(isset($week))
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="font-medium text-gray-700">Week Number:</span>
                    <span class="ml-2 text-gray-900">{{ $week->weeknumber }}</span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Year:</span>
                    <span class="ml-2 text-gray-900">{{ $week->year->year ?? '' }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form action="{{ route('storeowner.roster.updateweekroster', ['weekid' => base64_encode($week->weekid ?? 0), 'employeeid' => base64_encode($employee->employeeid)]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Day</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
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
                                    $weekRoster = $weekRosters[$day] ?? null;
                                    $startTime = $weekRoster && $weekRoster->start_time != '00:00:00' ? $weekRoster->start_time : 'off';
                                    $endTime = $weekRoster && $weekRoster->end_time != '00:00:00' ? $weekRoster->end_time : 'off';
                                    $dayDate = $weekRoster ? $weekRoster->day_date : '';
                                @endphp
                                <tr>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $day }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $dayDate ? date('d-m-Y', strtotime($dayDate)) : '-' }}</td>
                                    <td class="px-4 py-3">
                                        <select name="{{ $day }}_start" id="{{ $day }}_start" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                            <option value="off" {{ $startTime == 'off' ? 'selected' : '' }}>Off</option>
                                            @foreach($timeOptions as $time)
                                                <option value="{{ $time }}" {{ $startTime == $time ? 'selected' : '' }}>{{ date('H:i', strtotime($time)) }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="{{ $day }}_end" id="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500">
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
                    <a href="{{ route('storeowner.roster.viewweekroster', base64_encode($week->weekid ?? 0)) }}" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-storeowner-app-layout>

