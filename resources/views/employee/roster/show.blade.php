@section('page_header')
    @if($rosters->count() > 0)
        {{ ucfirst($rosters->first()->firstname ?? '') }} {{ ucfirst($rosters->first()->lastname ?? '') }}
    @else
        {{ __('View Roster') }}
    @endif
@endsection
<x-employee-app-layout>
    <!-- Breadcrumb -->
    <div class="mb-6">
        <nav class="flex text-gray-500 text-sm" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('employee.dashboard') }}" class="inline-flex items-center hover:text-gray-700">
                        <i class="fas fa-home mr-2"></i>
                        Home
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <a href="{{ route('employee.roster.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">My Roster</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">View</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Back Link -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('employee.roster.index') }}" class="text-orange-600 hover:text-orange-800">
            <i class="fas fa-angle-double-left mr-1"></i> Back
        </a>
    </div>

    @if($rosters->count() > 0)
        <!-- Week Navigation -->
        <div class="mb-6 bg-white rounded-lg shadow p-4">
            <form action="{{ route('employee.roster.navigate') }}" method="POST" class="flex items-center gap-4">
                @csrf
                <button type="submit" name="week_last" value="1" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    &lt;&lt;
                </button>
                <label class="font-medium text-gray-700">Week Number :</label>
                <label class="font-semibold text-gray-900">{{ $week }} - {{ $year }}</label>
                <button type="submit" name="week_next" value="1" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    &gt;&gt;
                </button>
                <input type="hidden" name="week" value="{{ $week }}">
                <input type="hidden" name="year" value="{{ $year }}">
                <input type="hidden" name="storeid" value="{{ $storeid }}">
                <input type="hidden" name="employeeid" value="{{ $employeeid }}">
                <input type="hidden" name="weekid" value="{{ $weekid }}">
            </form>
        </div>

        <!-- Roster Details Card -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div class="space-y-6">
                    @php
                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                        $rosterByDay = $rosters->keyBy('day');
                    @endphp

                    @foreach($days as $index => $day)
                        @php
                            $dayRoster = $rosterByDay->get($day);
                        @endphp
                        <div class="flex items-start gap-4 pb-4 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                            <div class="w-1/4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ $day }}</label>
                                <label class="block text-sm font-medium text-gray-700">Break</label>
                            </div>
                            <div class="w-3/4">
                                <div class="mb-2">
                                    <span class="text-sm text-gray-900">
                                        @if($dayRoster && $dayRoster->work_status != 'off')
                                            {{ date('H:i', strtotime($dayRoster->start_time ?? '00:00:00')) }} to {{ date('H:i', strtotime($dayRoster->end_time ?? '00:00:00')) }}
                                        @else
                                            OFF
                                        @endif
                                    </span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-900">
                                        @if($dayRoster && $dayRoster->work_status != 'off' && ($dayRoster->break_min ?? 0) != 0)
                                            Every {{ $dayRoster->break_every_hrs ?? 0 }} hrs {{ $dayRoster->break_min ?? 0 }} min
                                        @else
                                            -
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @else
        <!-- No Roster Message -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-gray-500">No Roster created</p>
        </div>
    @endif
</x-employee-app-layout>

