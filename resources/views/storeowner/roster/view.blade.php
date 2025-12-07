@section('page_header', 'View Roster')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">View Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">{{ $employee->firstname }} {{ $employee->lastname }}</h1>
        <a href="javascript:window.history.go(-1);" class="text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-1"></i> Back
        </a>
    </div>

    <!-- Roster Details -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start gap-4">
                    <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">Employee Name:</label>
                    <div class="w-3/4">
                        <p class="text-sm text-gray-900">{{ $employee->firstname }} {{ $employee->lastname }}</p>
                    </div>
                </div>

                @php
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    $rostersByDay = [];
                    foreach($rosters as $roster) {
                        $rostersByDay[$roster->day] = $roster;
                    }
                @endphp

                @foreach($days as $index => $day)
                    <div class="flex items-start gap-4">
                        <label class="w-1/4 pt-2 text-sm font-medium text-gray-700 text-end pr-5">{{ $day }}:</label>
                        <div class="w-3/4">
                            @if(isset($rostersByDay[$day]) && $rostersByDay[$day]->start_time != '00:00:00')
                                <p class="text-sm text-gray-900">
                                    {{ date('H:i', strtotime($rostersByDay[$day]->start_time)) }} to 
                                    {{ date('H:i', strtotime($rostersByDay[$day]->end_time)) }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500">OFF</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-storeowner-app-layout>

