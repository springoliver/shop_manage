<div id="editRosterTemplateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden" aria-labelledby="editRosterTemplateModalLabel" role="dialog" aria-modal="true">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-5xl mx-auto my-8 p-6">
        <div class="flex justify-between items-center border-b border-gray-200 pb-4 mb-4">
            <h5 class="text-lg font-semibold text-gray-800" id="editRosterTemplateModalLabel">Edit Roster Template</h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 text-2xl font-bold leading-none" onclick="closeTemplateModal()" aria-label="Close">
                &times;
            </button>
        </div>
        <form role="form" enctype="multipart/form-data" method="POST" action="{{ route('storeowner.roster.store') }}" id="editRosterTemplateForm">
            @csrf
            <div class="modal-body p-6">
                <input type="hidden" name="employeeid" id="employeeid" value="{{ $employee->employeeid }}"/>
                <input type="hidden" name="roster_week_hrs" id="roster_week_hrs" value="{{ $employee->roster_week_hrs ?? 0 }}" />
                <input type="hidden" name="roster_day_hrs" id="roster_day_hrs" value="{{ $employee->roster_day_hrs ?? 0 }}" />
                <input type="hidden" name="hdnmodal" id="hdnmodal" value="{{ $modelname ?? 'index' }}"/>
                
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
                                            $timeOptions[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
                                            $timeOptions[] = str_pad($i, 2, '0', STR_PAD_LEFT) . ':30';
                                        } else {
                                            $timeOptions[] = '23:59';
                                        }
                                    }
                                @endphp
                                @foreach($days as $index => $day)
                                    @php
                                        $roster = $weekroster[$index] ?? null;
                                        $startTime = $roster && $roster->start_time != '00:00:00' ? date('H:i', strtotime($roster->start_time)) : null;
                                        $endTime = $roster && $roster->end_time != '00:00:00' ? date('H:i', strtotime($roster->end_time)) : null;
                                        $workStatus = $roster ? $roster->work_status : 'off';
                                    @endphp
                                    <td class="px-2 py-3">
                                        <select id="{{ $day }}_start1" name="{{ $day }}_start" class="w-full mb-1 border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHourTemplateModal(this)">
                                            <option value="off" {{ $workStatus == 'off' ? 'selected' : '' }}>Off</option>
                                            @foreach($timeOptions as $time)
                                                @php
                                                    $timeValue = $time === '23:59' ? '23:59' : $time;
                                                    $timeDisplay = $time === '23:59' ? '24:00' : $time;
                                                @endphp
                                                <option value="{{ $timeValue }}" {{ $startTime == $time ? 'selected' : '' }}>{{ $timeDisplay }}</option>
                                            @endforeach
                                        </select>
                                        <select id="{{ $day }}_end1" name="{{ $day }}_end" class="w-full border border-gray-300 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-2 focus:ring-gray-500" onchange="checkWorkingHourTemplateModal(this)">
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

                <input type="hidden" name="total_hour" id="total_hour_template" value="0">
                <input type="hidden" name="total_minute" id="total_minute_template" value="0">
            </div>
            <div class="flex justify-end items-center border-t border-gray-200 pt-4 mt-4 px-6 pb-6">
                <button type="submit" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition mr-2" id="btnsubmit_template_model">
                    Save
                </button>
                <button type="button" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition" onclick="closeTemplateModal()">
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Vanilla JS for modal functionality
    function openTemplateModal() {
        document.getElementById('editRosterTemplateModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeTemplateModal() {
        document.getElementById('editRosterTemplateModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        // Remove the modal HTML from the DOM after closing
        const modalElement = document.getElementById('editRosterTemplateModal');
        if (modalElement && modalElement.parentNode) {
            modalElement.parentNode.remove();
        }
    }

    // Close modal on backdrop click
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('editRosterTemplateModal');
        if (modalElement) {
            modalElement.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeTemplateModal();
                }
            });
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const modal = document.getElementById('editRosterTemplateModal');
                if (modal && !modal.classList.contains('hidden')) {
                    closeTemplateModal();
                }
            }
        });
    });

    function compareTime(time1, time2) {
        const [h1, m1] = time1.split(':').map(Number);
        const [h2, m2] = time2.split(':').map(Number);
        if (h1 === h2) return m1 - m2;
        return h1 - h2;
    }

    function checkWorkingHourTemplateModal(obj) {
        const empid = document.getElementById('employeeid').value;
        const rosterWeekHrs = parseInt(document.getElementById('roster_week_hrs').value) || 0;
        const rosterDayHrs = parseInt(document.getElementById('roster_day_hrs').value) || 0;

        const dayName = obj.id.replace('_start1', '').replace('_end1', '');
        const startSelect = document.getElementById(dayName + '_start1');
        const endSelect = document.getElementById(dayName + '_end1');

        if (startSelect.value !== 'off' && endSelect.value !== 'off') {
            if (compareTime(endSelect.value, startSelect.value) <= 0) {
                alert('Please select valid Start/End Time of ' + dayName);
                endSelect.value = 'off';
                return false;
            }

            // Calculate hours for this day
            const start = new Date('2000-01-01 ' + startSelect.value + ':00');
            const end = new Date('2000-01-01 ' + endSelect.value + ':00');
            const diffHours = (end.getTime() - start.getTime()) / (1000 * 60 * 60);

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
            const start = document.getElementById(day + '_start1').value;
            const end = document.getElementById(day + '_end1').value;
            if (start !== 'off' && end !== 'off') {
                const startTime = new Date('2000-01-01 ' + start + ':00');
                const endTime = new Date('2000-01-01 ' + end + ':00');
                totalWeekHours += (endTime.getTime() - startTime.getTime()) / (1000 * 60 * 60);
            }
        });

        // Only validate week hours if both start and end are set (allow partial selection)
        if (startSelect.value !== 'off' && endSelect.value !== 'off') {
            if (rosterWeekHrs > 0 && rosterWeekHrs < totalWeekHours) {
                alert('Maximum roster week hours for this employee is over.');
                obj.value = 'off';
                return false;
            }
        }
        
        // Do NOT sync to 'off' here - let user freely select times
        // Sync only happens in the event listener when user explicitly selects 'off'
    }

    // Sync start/end time changes only when 'off' is explicitly selected
    const daysModal = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    daysModal.forEach(day => {
        const startSelect = document.getElementById(day + '_start1');
        const endSelect = document.getElementById(day + '_end1');

        if (startSelect) {
            startSelect.addEventListener('change', function() {
                // Only sync to 'off' if user explicitly selected 'off'
                // Allow changing from 'off' to a time without interference
                if (this.value === 'off') {
                    endSelect.value = 'off';
                }
                checkWorkingHourTemplateModal(this);
            });
        }

        if (endSelect) {
            endSelect.addEventListener('change', function() {
                // Only sync to 'off' if user explicitly selected 'off'
                // Allow changing from 'off' to a time without interference
                if (this.value === 'off') {
                    startSelect.value = 'off';
                }
                checkWorkingHourTemplateModal(this);
            });
        }
    });
</script>

