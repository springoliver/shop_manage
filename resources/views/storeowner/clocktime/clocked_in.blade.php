@section('page_header', 'Clocked-In-Employees')

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
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Clock-in-out</span>
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

    <!-- Page Title -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Clocked-In-Employees</h1>
    </div>

    <!-- Clocked In Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h5 class="text-lg font-semibold text-gray-800">Clocked In</h5>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Day</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start(Clock in-out App)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Finish(Clock in-out App)</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clock in-out App Hrs</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Manual Clock-out</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(count($clockedOutDetails) > 0)
                        @foreach($clockedOutDetails as $detail)
                            @php
                                $clockinDate = \Carbon\Carbon::parse($detail->clockin);
                                $dayName = $clockinDate->format('l');
                                $clockinTime = $clockinDate->format('H:i');
                                $clockoutTime = $detail->clockout ? \Carbon\Carbon::parse($detail->clockout)->format('H:i') : null;
                                $isStillWorking = $detail->status == 'clockout';
                                
                                // Calculate total hours
                                $totalHours = '---';
                                if (!$isStillWorking && $detail->timediff !== null) {
                                    $hours = floor($detail->timediff / 60);
                                    $minutes = $detail->timediff % 60;
                                    $totalHours = $hours . ' hours ' . $minutes . ' minutes';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $detail->department ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    <a href="{{ route('storeowner.clocktime.week-clock-time', [
                                        'employeeid' => base64_encode($detail->employeeid),
                                        'date' => $clockinDate->format('Y-m-d')
                                    ]) }}" 
                                       class="text-blue-600 hover:text-blue-800 hover:underline"
                                       title="Click here to manage clock in-out of {{ ucfirst($detail->firstname) }} {{ ucfirst($detail->lastname) }}">
                                        {{ ucfirst($detail->firstname) }} {{ ucfirst($detail->lastname) }}
                                    </a>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $clockinDate->format('Y-m-d') }}<br/>
                                    <span class="text-gray-500">({{ $dayName }})</span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" title="{{ $clockinTime }}">
                                    {{ $clockinTime }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900" title="{{ $clockoutTime ?? 'Still Working' }}">
                                    @if($isStillWorking)
                                        <span class="text-orange-600 font-semibold">Still Working...</span>
                                    @else
                                        {{ $clockoutTime }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    {{ $totalHours }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                                    @if($isStillWorking)
                                        <button type="button" 
                                                class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500"
                                                onclick="confirmClockOut({{ $detail->eltid }})"
                                                title="Click to Clock-out employee">
                                            Clock-out
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                No records found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Clock-out Confirmation Modals -->
    @if(count($clockedOutDetails) > 0)
        @foreach($clockedOutDetails as $detail)
            @if($detail->status == 'clockout')
            <div id="confirm-status-{{ $detail->eltid }}" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50" style="display: none;">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Clock-out Employee</h3>
                                <button type="button" onclick="closeClockOutModal({{ $detail->eltid }})" class="text-gray-400 hover:text-gray-600">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <form action="{{ route('storeowner.clocktime.manual-clockout') }}" method="POST">
                            @csrf
                            <div class="px-6 py-4">
                                <div class="mb-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" name="status" value="clockout" checked class="form-radio">
                                        <span class="ml-2 text-gray-700">Clock-out</span>
                                    </label>
                                </div>
                                <input type="hidden" name="eltid" value="{{ $detail->eltid }}">
                            </div>
                            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                                <button type="button" 
                                        onclick="closeClockOutModal({{ $detail->eltid }})" 
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                    No
                                </button>
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                                    Yes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    @endif

    @push('scripts')
    <script>
        function confirmClockOut(eltid) {
            const modal = document.getElementById('confirm-status-' + eltid);
            if (modal) {
                modal.style.display = 'block';
            }
        }

        function closeClockOutModal(eltid) {
            const modal = document.getElementById('confirm-status-' + eltid);
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Close modal when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('bg-gray-600')) {
                const modals = document.querySelectorAll('[id^="confirm-status-"]');
                modals.forEach(modal => {
                    modal.style.display = 'none';
                });
            }
        });
    </script>
    @endpush
</x-storeowner-app-layout>

