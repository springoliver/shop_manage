@section('page_header', 'Search & Print Roster')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Search & Print</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Search Form -->
    <div class="flex justify-between my-6 ">
        <div></div>
        <form action="{{ route('storeowner.roster.searchprintroster') }}" method="POST" class="flex items-center gap-4">
            @csrf
            <label for="dateofbirth" class="text-sm font-medium text-gray-700">Select Week:</label>
            <input type="date" 
                    name="dateofbirth" 
                    id="dateofbirth" 
                    value="{{ $dateofbirth ?? date('Y-m-d') }}" 
                    required
                    class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
                Search
            </button>
        </form>
    </div>

    <!-- Header -->
    <div class="text-center mb-6">
        <button onclick="window.print()" class="px-4 py-2 float-left bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition">
            Print
        </button>
        <h1 class="text-2xl font-bold text-gray-800">Roster for Week {{ $weeknumber }} / {{ $year }}</h1>
    </div>

    <!-- Roster Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Sunday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Monday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tuesday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Wednesday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Thursday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Friday</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Saturday</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        @endphp
                        @forelse($rostersByEmployee as $employeeId => $weekRosters)
                            @php
                                $employee = $employees->firstWhere('employeeid', $employeeId);
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
                                    <td class="px-4 py-3 text-sm text-gray-900 text-center">
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
                                <td colspan="8" class="px-4 py-6 text-center text-gray-500">No roster found for this week</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        @media print {
            .no-print { 
                display: none !important; 
            }
            body { 
                margin: 0; 
                padding: 20px; 
            }
            table {
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
        }
    </style>
    @endpush
</x-storeowner-app-layout>
