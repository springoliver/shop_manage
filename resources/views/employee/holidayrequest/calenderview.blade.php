@section('page_header', 'Time Off Request')
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
                        <a href="{{ route('employee.holidayrequest.index') }}" class="ml-1 text-gray-500 hover:text-gray-700 md:ml-2">Time Off Request</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400"></i>
                        <span class="ml-1 font-medium text-gray-700 md:ml-2">Calendar View</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Calendar</h1>
    </div>

    <!-- Calendar -->
    <div class="bg-white rounded-lg shadow p-6">
        <div id="calendar"></div>
    </div>

    @push('styles')
    <link rel="stylesheet" href="{{ asset('css/fullcalendar/main.min.css') }}">
    <style>
        .fc {
            font-family: inherit;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [fullCalendarPlugins.dayGrid, fullCalendarPlugins.interaction],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    fetch('{{ route("employee.holidayrequest.get-requests") }}?from_date=' + Math.floor(fetchInfo.start.getTime() / 1000) + '&to_date=' + Math.floor(fetchInfo.end.getTime() / 1000))
                        .then(response => response.json())
                        .then(data => {
                            successCallback(data.events);
                        })
                        .catch(error => {
                            console.error('Error fetching events:', error);
                            failureCallback(error);
                        });
                },
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit'
                }
            });
            calendar.render();
        });
    </script>
    @endpush
</x-employee-app-layout>

