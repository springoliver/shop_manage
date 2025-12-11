<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Roster - Week {{ $weeknumber }} / {{ $year }}</title>
    <style>
        @media print {
            body { margin: 0; padding: 20px; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #4CAF50; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Print
        </button>
        <a href="{{ route('storeowner.roster.index') }}" style="padding: 10px 20px; background-color: #2196F3; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">
            Back
        </a>
    </div>

    <div class="header">
        <h1>Roster for Week {{ $weeknumber }} / {{ $year }}</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee Name</th>
                <th>Sunday</th>
                <th>Monday</th>
                <th>Tuesday</th>
                <th>Wednesday</th>
                <th>Thursday</th>
                <th>Friday</th>
                <th>Saturday</th>
            </tr>
        </thead>
        <tbody>
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
                <tr>
                    <td><strong>{{ $employee->firstname }} {{ $employee->lastname }}</strong></td>
                    @foreach($days as $day)
                        <td>
                            @if(isset($rosterByDay[$day]) && $rosterByDay[$day]->start_time != '00:00:00')
                                {{ date('H:i', strtotime($rosterByDay[$day]->start_time)) }} - 
                                {{ date('H:i', strtotime($rosterByDay[$day]->end_time)) }}
                            @else
                                OFF
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">No roster found for this week</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
