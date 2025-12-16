<!DOCTYPE html>
<html lang="en">
<head>
    <title>All Employee Hours & Holiday Summary</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="row">
        <div class="col-xs-12">
            <h2 style="text-align: center; margin-bottom: 20px;">All Employee Hours & Holiday Summary</h2>
            
            <div class="widget-content nopadding">
                <table class="table pull-left table-bordered table-striped table-hover data-table" id="table-new">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Employee Name</th>
                            <th>Salary Method</th>
                            <th>Hours Worked</th>
                            <th>Due Holidays</th>
                            <th>Extra Holiday Hrs (Bank Hol. etc)</th>
                            <th>Holidays Taken</th>
                            <th>Holidays Remaining</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @if(count($empPayrollHrs) > 0)
                            @foreach($empPayrollHrs as $payroll)
                                <tr>
                                    <td data-th="Year" title="{{ $payroll->year }}">
                                        {{ $payroll->year }}
                                    </td>
                                    <td data-th="Employee Name" title="{{ $payroll->firstname }}">
                                        {{ $payroll->firstname }} {{ $payroll->lastname }}
                                    </td>
                                    
                                    <td data-th="Salary Method">
                                        {{ ucfirst($payroll->sallary_method) }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format((float)$payroll->hours_worked, 2) }}
                                    </td>
                                    
                                    @if($payroll->sallary_method == 'hourly')
                                        <td>
                                            {{ floor((float)$payroll->holiday_calculated) }} hrs
                                        </td>
                                    @endif
                                    
                                    @if($payroll->sallary_method == 'yearly')
                                        <td data-th="Holiday (Days) Taken">
                                            {{ number_format((float)$payroll->holiday_days_counted, 2) }} days
                                        </td>
                                    @endif
                                    
                                    <td data-th="Extra Holidays">
                                        {{ floor((float)$payroll->extra_holiday_calculated) }}
                                    </td>
                                    
                                    @if($payroll->sallary_method == 'hourly')
                                        <td data-th="Holiday (Hrs) Taken">
                                            {{ number_format((float)$payroll->holiday_hrs, 2) }} hrs
                                        </td>
                                    @endif
                                    
                                    @if($payroll->sallary_method == 'yearly')
                                        <td data-th="Holiday (Days) Taken">
                                            {{ number_format((float)$payroll->holiday_days, 2) }} days
                                        </td>
                                    @endif
                                    
                                    @if($payroll->sallary_method == 'hourly')
                                        <td data-th="Holiday (Hrs) Remaining">
                                            {{ floor((float)$payroll->holiday_due ?? ((float)$payroll->holiday_calculated - (float)$payroll->holiday_hrs)) }}
                                        </td>
                                    @endif
                                    
                                    @if($payroll->sallary_method == 'yearly')
                                        <td data-th="Holiday (Days) Remaining">
                                            {{ number_format((float)$payroll->holiday_days_counted - (float)$payroll->holiday_days, 2) }}
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

