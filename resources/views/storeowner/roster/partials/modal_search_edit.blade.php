<div class="fixed inset-0 z-50 overflow-y-auto hidden" id="editRosterModal" role="dialog" aria-labelledby="editRosterModalLabel">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-gray-50 border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h5 class="text-lg font-semibold text-gray-800" id="editRosterModalLabel">Search & Edit Week Roster</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none close" data-bs-dismiss="modal" aria-label="Close" style="background: none; border: none; cursor: pointer;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form role="form" enctype="multipart/form-data" method="POST" action="{{ route('storeowner.roster.addeditweekroster') }}">
                @csrf
                <div class="p-6">
                    <input type="hidden" name="employeeid" id="employeeid" value="{{ $employee->employeeid }}"/>
                    <input type="hidden" name="roster_week_hrs" id="roster_week_hrs" value="{{ $employee->roster_week_hrs ?? 0 }}" />
                    <input type="hidden" name="roster_day_hrs" id="roster_day_hrs" value="{{ $employee->roster_day_hrs ?? 0 }}" />
                    @if(isset($weekid))
                        <input type="hidden" name="hdnweekid" id="hdnweekid" value="{{ $weekid }}"/>
                    @endif
                    @if(isset($dateInput))
                        <input type="hidden" name="dateofbirth" id="dateofbirth_modal" value="{{ $dateInput }}"/>
                    @endif
                    
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700">
                            Employee Name: <span class="font-semibold">{{ $employee->firstname }} {{ $employee->lastname }}</span>
                        </p>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sunday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Monday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tuesday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Wednesday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thursday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Friday</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Saturday</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    @php
                                        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                                        $timeOptions = [];
                                        for ($i = 1; $i <= 24; $i++) {
                                            if ($i != 24) {
                                                $timeOptions[] = $i . ':00';
                                                $timeOptions[] = $i . ':30';
                                            } else {
                                                $timeOptions[] = '23:59';
                                            }
                                        }
                                    @endphp
                                    @foreach($days as $index => $day)
                                        @php
                                            $roster = $rosters[$day] ?? null;
                                            $startTime = $roster && $roster->start_time != '00:00:00' ? date('G:i', strtotime($roster->start_time)) : null;
                                            $endTime = $roster && $roster->end_time != '00:00:00' ? date('G:i', strtotime($roster->end_time)) : null;
                                            $workStatus = $roster ? $roster->work_status : 'off';
                                        @endphp
                                        <td class="px-2 py-3">
                                            <select id="{{ $day }}_start1" name="{{ $day }}_start" class="w-full mb-1 border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHourModel(this)">
                                                <option value="off" {{ $workStatus == 'off' ? 'selected' : '' }}>Off</option>
                                                @foreach($timeOptions as $time)
                                                    @php
                                                        $timeValue = $time === '23:59' ? '23:59' : $time;
                                                        $timeDisplay = $time === '23:59' ? '24:00' : $time;
                                                    @endphp
                                                    <option value="{{ $timeValue }}" {{ $startTime == $time ? 'selected' : '' }}>{{ $timeDisplay }}</option>
                                                @endforeach
                                            </select>
                                            <select id="{{ $day }}_end1" name="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHourModel(this)">
                                                <option value="off" {{ $workStatus == 'off' ? 'selected' : '' }}>Off</option>
                                                @foreach($timeOptions as $time)
                                                    @php
                                                        $timeValue = $time === '23:59' ? '23:59' : $time;
                                                        $timeDisplay = $time === '23:59' ? '24:00' : $time;
                                                    @endphp
                                                    <option value="{{ $timeValue }}" {{ $endTime == $time ? 'selected' : '' }}>{{ $timeDisplay }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                    @endforeach
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <input type="hidden" name="total_hour" id="total_hour_modal" value="0">
                    <input type="hidden" name="total_minute" id="total_minute_modal" value="0">
                </div>
                <div class="bg-gray-50 border-t border-gray-200 px-6 py-4 flex justify-end gap-3">
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition" id="btnsubmit_model">
                        Save
                    </button>
                    <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition close" data-bs-dismiss="modal">
                        Close
                    </button>
                </div>
            </form>
            
            <script>
            // Auto-submit the search form after successful save to refresh the table (matching CI behavior)
            // This ensures the table is refreshed with updated data after saving
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.querySelector('#editRosterModal form');
                if (form) {
                    form.addEventListener('submit', function(e) {
                        // Let the form submit normally
                        // After redirect, the page will reload with the date parameter
                    });
                }
            });
            </script>
        </div>
    </div>
</div>

<script>
// Define function globally so it's accessible from inline onchange handlers
window.checkWorkingHourModel = function(obj) {
    const empid = document.getElementById('employeeid');
    if (!empid) {
        console.error('Employee ID element not found');
        return false;
    }
    
    const rosterWeekHrs = parseInt(document.getElementById('roster_week_hrs').value) || 0;
    const rosterDayHrs = parseInt(document.getElementById('roster_day_hrs').value) || 0;
    
    const dayName = obj.id.replace('_start1', '').replace('_end1', '');
    const startSelect = document.getElementById(dayName + '_start1');
    const endSelect = document.getElementById(dayName + '_end1');
    
    if (!startSelect || !endSelect) {
        console.error('Start or end select not found for day:', dayName);
        return false;
    }
    
    if (startSelect.value !== 'off' && endSelect.value !== 'off') {
        const startTime = startSelect.value.replace(':', '.');
        const endTime = endSelect.value.replace(':', '.');
        
        if (parseFloat(endTime) <= parseFloat(startTime)) {
            alert('Please select valid Start/End Time of ' + dayName);
            endSelect.value = 'off';
            return false;
        }
        
        // Calculate hours for this day
        const start = new Date('2000-01-01 ' + startSelect.value + ':00').getTime();
        const end = new Date('2000-01-01 ' + endSelect.value + ':00').getTime();
        const diffHours = (end - start) / (1000 * 60 * 60);
        
        if (rosterDayHrs > 0 && rosterDayHrs < diffHours) {
            alert('Maximum roster day hours for this employee is over.');
            obj.value = 'off';
            return false;
        }
    }
    
    // Calculate total week hours
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    let totalWeekHours = 0;
    days.forEach(day => {
        const startEl = document.getElementById(day + '_start1');
        const endEl = document.getElementById(day + '_end1');
        if (startEl && endEl) {
            const start = startEl.value;
            const end = endEl.value;
            if (start !== 'off' && end !== 'off') {
                const startTime = new Date('2000-01-01 ' + start + ':00').getTime();
                const endTime = new Date('2000-01-01 ' + end + ':00').getTime();
                totalWeekHours += (endTime - startTime) / (1000 * 60 * 60);
            }
        }
    });
    
    if (rosterWeekHrs > 0 && rosterWeekHrs < totalWeekHours) {
        alert('Maximum roster week hours for this employee is over.');
        obj.value = 'off';
        return false;
    }
    
    // Sync start/end changes - if one is set to "off", the other should also be "off"
    // Only sync if the current value is "off" (don't interfere with selection)
    if (obj.id.includes('_start1') && obj.value === 'off') {
        // User selected "off" in start dropdown
        endSelect.value = 'off';
    } else if (obj.id.includes('_end1') && obj.value === 'off') {
        // User selected "off" in end dropdown
        startSelect.value = 'off';
    }
    
    return true;
};

// Initialize modal after it's loaded - no additional sync needed as checkWorkingHourModel handles it
</script>

