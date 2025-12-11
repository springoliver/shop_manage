<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clock In Out Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
        }
        h2 {
            margin: 0 0 10px 0;
            font-size: 16px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead {
            display: table-header-group;
        }
        thead tr {
            page-break-inside: avoid;
        }
        tbody tr {
            page-break-inside: avoid;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .header-info {
            margin-bottom: 15px;
        }
        .header-info table {
            border: none;
            width: auto;
            margin-top: 0;
        }
        .header-info th, .header-info td {
            border: none;
            padding: 3px 10px 3px 0;
            white-space: nowrap;
            display: table-cell;
            vertical-align: top;
            word-wrap: normal;
            word-break: keep-all;
            letter-spacing: normal;
        }
        .header-info th {
            text-align: left;
        }
        .header-info table {
            table-layout: auto;
        }
    </style>
</head>
<body>
    <div class="header-info">
        <h2>Clock In Out Report</h2>
        <div style="margin-top: 10px;">
            <strong>Start Date:</strong> {{ \Carbon\Carbon::parse($startdate)->format('Y-m-d') }} &nbsp;&nbsp;&nbsp;&nbsp;
            <strong>End Date:</strong> {{ \Carbon\Carbon::parse($enddate)->format('Y-m-d') }}
            @if(count($clockdetails) > 0)
                @php
                    $firstDetail = is_array($clockdetails) ? (object)($clockdetails[0] ?? []) : ($clockdetails[0] ?? null);
                    $weekid = $firstDetail->weekid ?? null;
                    if ($weekid) {
                        // Get week number from weekid using the service
                        $weekNo = app(\App\Services\StoreOwner\ClockTimeService::class)->getWeekById($weekid);
                        if (!$weekNo) {
                            // Fallback to calculating week number from end date
                            $weekNo = \Carbon\Carbon::parse($enddate)->format('W');
                        }
                    } else {
                        $weekNo = \Carbon\Carbon::parse($enddate)->format('W');
                    }
                @endphp
                &nbsp;&nbsp;&nbsp;&nbsp; <strong>Week</strong> {{ $weekNo }}-{{ \Carbon\Carbon::parse($enddate)->format('Y') }}
            @endif
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Employee Name</th>
                <th>Day</th>
                <th>Start(Roster)</th>
                <th>Finish(Roster)</th>
                <th>Start(Clock in-out App)</th>
                <th>Finish(Clock in-out App)</th>
                <th>Total (Numeric)</th>
            </tr>
        </thead>
        <tbody>
            @if(count($clockdetails) > 0)
                @php
                    $clockdetailsArray = is_array($clockdetails) ? $clockdetails : $clockdetails->toArray();
                @endphp
                @foreach($clockdetailsArray as $detail)
                    @php
                        $detail = (object) $detail; // Ensure it's an object
                        $clockinDate = $detail->clockin ? \Carbon\Carbon::parse($detail->clockin) : null;
                        $clockoutDate = ($detail->status !== 'clockout' && $detail->clockout) ? \Carbon\Carbon::parse($detail->clockout) : null;
                    @endphp
                    <tr>
                        <td>{{ $clockinDate ? $clockinDate->format('Y-m-d') : 'N/A' }}</td>
                        <td>{{ ucfirst($detail->firstname ?? '') }} {{ ucfirst($detail->lastname ?? '') }}</td>
                        <td>{{ $detail->day ?? 'N/A' }}</td>
                        <td>{{ $detail->roster_start_time ?? '00:00' }}</td>
                        <td>{{ $detail->roster_end_time ?? '00:00' }}</td>
                        <td>
                            @if($clockinDate)
                                {{ $clockinDate->format('Y-m-d H:i') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            @if($clockoutDate)
                                {{ $clockoutDate->format('Y-m-d H:i') }}
                            @else
                                Still Working...
                            @endif
                        </td>
                        <td>
                            @if($detail->status !== 'clockout' && isset($detail->total) && $detail->total)
                                {{ number_format((float)$detail->total, 2) }}
                            @else
                                Still Working...
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="8" style="text-align: center;">No clock in-out records found</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>

