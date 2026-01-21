@section('page_header', 'Add Week Roster')

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
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Add Week Roster</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Add Week Roster</h1>
        <div class="flex items-center space-x-3">
            <a href="#" id="emailRosterBtn" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 flex items-center" title="Email Roster">
                <i class="fas fa-envelope mr-2"></i>
                Email Roster
            </a>
            <span class="text-gray-400">-</span>
            <button type="submit" form="rosterForm" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700" title="Save">
                Save
            </button>
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

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <div class="p-6">
            <form id="rosterForm" action="{{ route('storeowner.roster.weekrosteradd') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Week:</label>
                        <input type="date" name="weeknumber" id="weeknumber" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gray-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Week No:</label>
                        <label id="week_num" class="block text-sm text-gray-900 pt-2">-</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Week Start:</label>
                        <label id="week_start" class="block text-sm text-gray-900 pt-2">-</label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Week End:</label>
                        <label id="week_end" class="block text-sm text-gray-900 pt-2">-</label>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Employee</th>
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
                            @forelse($employees as $emp)
                                @php
                                    $employeeRosters = $weekroster->where('employeeid', $emp->employeeid);
                                    $rosterByDay = [];
                                    foreach($employeeRosters as $roster) {
                                        $rosterByDay[$roster->day] = $roster;
                                    }
                                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                        {{ $emp->firstname }} {{ $emp->lastname }}
                                    </td>
                                    @foreach($days as $day)
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            @if(isset($rosterByDay[$day]) && $rosterByDay[$day]->start_time != '00:00:00')
                                                {{ date('H:i', strtotime($rosterByDay[$day]->start_time)) }} to 
                                                {{ date('H:i', strtotime($rosterByDay[$day]->end_time)) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">No employees with rosters found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let selectedWeekDate = null;
        
        document.getElementById('weeknumber').addEventListener('change', function() {
            const dateValue = this.value;
            if (!dateValue) {
                selectedWeekDate = null;
                return;
            }

            selectedWeekDate = dateValue;
            const date = new Date(dateValue);
            const year = date.getFullYear();
            
            // Get week number using ISO week
            const startDate = new Date(year, 0, 1);
            const days = Math.floor((date - startDate) / (24 * 60 * 60 * 1000));
            const weekNumber = Math.ceil((days + startDate.getDay() + 1) / 7);

            // Get week start (Monday)
            const day = date.getDay();
            const diff = date.getDate() - day + (day == 0 ? -6 : 1);
            const weekStart = new Date(date.setDate(diff));
            
            // Get week end (Sunday)
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekEnd.getDate() + 6);

            document.getElementById('week_num').textContent = weekNumber;
            document.getElementById('week_start').textContent = formatDate(weekStart);
            document.getElementById('week_end').textContent = formatDate(weekEnd);
        });

        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }
        
        // Handle Email Roster button click
        document.getElementById('emailRosterBtn').addEventListener('click', function(e) {
            e.preventDefault();
            let emailUrl = '{{ route("storeowner.roster.email") }}';
            
            if (selectedWeekDate) {
                emailUrl += '?date=' + encodeURIComponent(selectedWeekDate);
            }
            
            window.location.href = emailUrl;
        });
    </script>
    @endpush
</x-storeowner-app-layout>

