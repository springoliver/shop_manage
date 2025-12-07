<?php

namespace App\Services\StoreOwner;

use App\Models\EmpLoginTime;
use App\Models\EmpPayrollHrs;
use App\Models\StoreEmployee;
use App\Models\WeekRoster;
use App\Models\Week;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClockTimeService
{
    /**
     * Get clock details for current date.
     */
    public function getClockDetails($storeid)
    {
        $date = Carbon::now()->format('Y-m-d');
        
        $clockDetails = EmpLoginTime::with(['employee', 'week'])
            ->where('storeid', $storeid)
            ->where(function($query) use ($date) {
                $query->whereDate('clockin', $date)
                      ->orWhereDate('clockout', $date);
            })
            ->whereHas('employee', function($q) {
                $q->where('status', '!=', 'Deactivate');
            })
            ->orderBy('clockin', 'DESC')
            ->get()
            ->groupBy('employeeid')
            ->map(function($group) {
                return $group->first();
            })
            ->values();

        // Add roster times
        foreach ($clockDetails as $detail) {
            $rosterData = $this->getRosterHour($detail->employeeid, $detail->weekid, $detail->day);
            $detail->roster_start_time = $rosterData['start_time'] ?? '00:00';
            $detail->roster_end_time = $rosterData['end_time'] ?? '00:00';
        }

        return $clockDetails;
    }

    /**
     * Get clock details by date range and employee filter.
     */
    public function getClockDetailsByDate($storeid, $date, $enddate = null, $employeeids = [])
    {
        $query = EmpLoginTime::with(['employee', 'week'])
            ->where('storeid', $storeid)
            ->where('inRoster', 'Yes')
            ->whereHas('employee', function($q) {
                $q->where('status', '!=', 'Deactivate');
            });

        if ($enddate) {
            $query->where(function($q) use ($date, $enddate) {
                $q->whereBetween(DB::raw('Date(clockin)'), [$date, $enddate])
                  ->orWhereBetween(DB::raw('Date(clockout)'), [$date, $enddate]);
            });
        } else {
            $query->where(function($q) use ($date) {
                $q->whereDate('clockin', $date)
                  ->orWhereDate('clockout', $date);
            });
        }

        if (!empty($employeeids)) {
            $query->whereIn('employeeid', $employeeids);
        }

        return $query->orderBy('clockin', 'DESC')->get();
    }

    /**
     * Get roster hour for employee, week, and day.
     */
    public function getRosterHour($employeeid, $weekid, $day)
    {
        $roster = WeekRoster::where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->where('day', $day)
            ->first();

        if ($roster) {
            return [
                'start_time' => Carbon::parse($roster->start_time)->format('H:i'),
                'end_time' => Carbon::parse($roster->end_time)->format('H:i'),
            ];
        }

        return [
            'start_time' => '00:00',
            'end_time' => '00:00',
        ];
    }

    /**
     * Get or create year and week.
     */
    public function getOrCreateYearAndWeek($date)
    {
        $carbonDate = Carbon::parse($date);
        $year = $carbonDate->year;
        $weekNumber = $carbonDate->week;

        // Get or create year
        $yearModel = Year::firstOrCreate(['year' => (string)$year]);

        // Get or create week
        $weekModel = Week::firstOrCreate(
            [
                'weeknumber' => $weekNumber,
                'yearid' => $yearModel->yearid,
            ]
        );

        return [
            'yearid' => $yearModel->yearid,
            'weekid' => $weekModel->weekid,
            'day' => $carbonDate->format('l'),
        ];
    }

    /**
     * Calculate total hours between two times.
     */
    public function calculateHours($startTime, $endTime)
    {
        if (!$startTime || !$endTime || $startTime === '00:00:00' || $endTime === '00:00:00') {
            return 0;
        }

        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        return round($end->diffInMinutes($start) / 60, 2);
    }

    /**
     * Get employee holiday calculation.
     * Groups by year and employee, calculates holiday entitlements and usage.
     */
    public function getEmployeeHolidayCalculation($storeid)
    {
        // Get data from the last year
        $oneYearAgo = Carbon::now()->subYear();
        
        $results = DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.year',
                'ep.employeeid',
                'e.firstname',
                'e.lastname',
                'e.sallary_method',
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2))) AS hours_worked'),
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2)) * 8 / 100) AS holiday_calculated'),
                DB::raw('SUM(COALESCE(ep.holiday_hrs, 0)) AS holiday_hrs'),
                DB::raw('SUM(COALESCE(ep.holiday_days, 0)) AS holiday_days'),
                DB::raw('SUM(COALESCE(ep.extras1_hrs, 0) + COALESCE(ep.owertime1_hrs, 0) + COALESCE(ep.owertime2_hrs, 0)) AS extra_holiday_calculated'),
                DB::raw('COUNT(ep.hours_worked) / 2.6 AS holiday_days_counted'),
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2)) * 8 / 100 + COALESCE(ep.extras1_hrs, 0) + COALESCE(ep.owertime1_hrs, 0) + COALESCE(ep.owertime2_hrs, 0) - COALESCE(ep.holiday_hrs, 0)) AS holiday_due'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('e.status', '!=', 'Deactivate')
            ->where('ep.year', '>=', $oneYearAgo->year)
            ->where('ep.year', '<=', Carbon::now()->year)
            ->groupBy('ep.year', 'ep.employeeid', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->orderBy('ep.year', 'DESC')
            ->get();

        return $results;
    }

    /**
     * Get all week hours grouped by weekno and year.
     */
    public function getAllWeekHrs($storeid)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.weekno',
                'ep.year',
                'ep.week_start',
                'ep.week_end',
                'ep.employeeid',
                'ep.payroll_id',
                'e.firstname',
                'e.lastname',
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2))) AS hours_worked'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('e.status', '!=', 'Deactivate')
            ->groupBy('ep.weekno', 'ep.year', 'ep.employeeid', 'ep.payroll_id', 'ep.week_start', 'ep.week_end', 'e.firstname', 'e.lastname')
            ->orderBy('ep.week_start', 'DESC')
            ->orderBy('ep.year', 'DESC')
            ->get();
    }

    /**
     * Get all employees weekly hours grouped by weekno and year.
     */
    public function getAllEmployeesWeekHrs($storeid)
    {
        return DB::table('stoma_emp_payroll_hrs')
            ->select([
                'weekno',
                'year',
                'week_start',
                'week_end',
                DB::raw('SUM(CAST(hours_worked AS DECIMAL(10,2))) AS hours_worked'),
            ])
            ->where('storeid', $storeid)
            ->groupBy('weekno', 'year', 'week_start', 'week_end')
            ->orderBy('weekno', 'DESC')
            ->orderBy('year', 'DESC')
            ->get();
    }

    /**
     * Get monthly hours for all employees grouped by month and year.
     */
    public function getMonthlyHrsAllEmployee($storeid)
    {
        return DB::table('stoma_emp_payroll_hrs')
            ->select([
                DB::raw('MONTH(week_start) AS month'),
                DB::raw('YEAR(week_start) AS year'),
                DB::raw('MIN(week_start) AS week_start'),
                DB::raw('SUM(CAST(hours_worked AS DECIMAL(10,2))) AS hours_worked'),
            ])
            ->where('storeid', $storeid)
            ->groupBy(DB::raw('MONTH(week_start)'), DB::raw('YEAR(week_start)'))
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get();
    }
}

