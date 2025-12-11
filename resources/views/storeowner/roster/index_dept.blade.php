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

    <!-- Header -->
    <div class="flex justify-between items-center mb-2">
        <!-- Department Filter Buttons -->
        @if($departments->count() > 0)
            <div class="mb-4 flex flex-wrap gap-2">
                @foreach($departments as $dept)
                    <a href="{{ route('storeowner.roster.index-dept', base64_encode($dept->departmentid)) }}" 
                    class="px-4 py-2 {{ $departmentid == $dept->departmentid ? 'bg-blue-700' : 'bg-blue-600' }} text-white text-sm font-medium rounded-md hover:bg-blue-700 transition">
                        {{ $dept->department }} Roster
                    </a>
                @endforeach
            </div>
        @endif

        <div class="flex space-x-3">
            <a href="{{ route('storeowner.roster.weekroster') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:bg-gray-700">
                <i class="fas fa-plus mr-2"></i>
                Add Week Roster
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
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
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
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
                                        <a href="{{ route('storeowner.roster.edit', base64_encode($employee->employeeid)) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
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
                                <td colspan="10" class="px-4 py-6 text-center text-gray-500">No rosters found for this department</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

