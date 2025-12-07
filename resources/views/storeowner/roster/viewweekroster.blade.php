@section('page_header', 'Current Roster')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Week Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Navigation Buttons -->
    <div class="mb-4 flex flex-wrap gap-2">
        <a href="{{ route('storeowner.roster.index') }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            Roster Template
        </a>
        <a href="{{ route('storeowner.roster.viewweekroster', base64_encode($week->weekid ?? 0)) }}" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            Current Roster
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Current Roster</h1>
        <a href="{{ route('storeowner.roster.weekroster') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
            <i class="fas fa-plus mr-2"></i>
            Add
        </a>
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

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
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
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @forelse($rostersByEmployee as $employeeId => $weekRosters)
                            @php
                                $employee = $weekRosters->first()->employee ?? null;
                                if (!$employee) continue;
                                $rosterByDay = [];
                                foreach($weekRosters as $roster) {
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
                                            {{ date('H:i', strtotime($rosterByDay[$day]->start_time)) }} - 
                                            {{ date('H:i', strtotime($rosterByDay[$day]->end_time)) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">No weekly rosters found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

