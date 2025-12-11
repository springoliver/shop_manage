<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Weekly Roster</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2c3e50;">Hello {{ $employee->firstname }} {{ $employee->lastname }},</h2>
        
        <p>Your weekly roster for Week {{ $weeknumber }} / {{ $year }} has been scheduled. Please find your schedule below:</p>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0; border: 1px solid #ddd;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Day</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Date</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Time</th>
                    <th style="border: 1px solid #ddd; padding: 12px; text-align: left;">Break</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                    $my_roster_array = $my_roster->values()->all();
                @endphp
                @for($i = 0; $i < 7; $i++)
                    @php
                        $roster = $my_roster_array[$i] ?? null;
                    @endphp
                    <tr>
                        <td style="border: 1px solid #ddd; padding: 12px;"><strong>{{ $days[$i] }}</strong></td>
                        <td style="border: 1px solid #ddd; padding: 12px;">
                            {{ $roster && isset($roster->day_date) ? date('d-m-Y', strtotime($roster->day_date)) : '-' }}
                        </td>
                        <td style="border: 1px solid #ddd; padding: 12px;">
                            @if($roster && $roster->work_status != 'off')
                                {{ date('H:i', strtotime($roster->start_time)) }} to {{ date('H:i', strtotime($roster->end_time)) }}
                            @else
                                OFF
                            @endif
                        </td>
                        <td style="border: 1px solid #ddd; padding: 12px;">
                            @if($roster && $roster->work_status != 'off' && $roster->break_min != 0)
                                Every {{ $roster->break_every_hrs }} hrs {{ $roster->break_min }} min
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @endfor
            </tbody>
        </table>
        
        <p style="margin-top: 20px;">Best regards,<br>{{ $sitename ?? config('app.name') }}</p>
    </div>
</body>
</html>
