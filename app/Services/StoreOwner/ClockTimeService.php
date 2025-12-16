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
     * Get clock report for PDF export (grouped by weekid and day like CI).
     */
    public function getClockReportForExport($storeid, $date, $enddate = null, $employeeids = [])
    {
        // Build query exactly like CI (line 853-871) for maximum performance
        $sql = "SELECT el.employeeid, e.firstname, e.lastname, el.weekid, el.day, el.status, "
             . "MIN(el.clockin) as clockin, MAX(el.clockout) as clockout, "
             . "SUM(TIMESTAMPDIFF(MINUTE, el.clockin, el.clockout)) as timediff, "
             . "wr.start_time as roster_start_time, wr.end_time as roster_end_time, "
             . "MIN(el.eltid) as eltid, el.storeid "
             . "FROM stoma_emp_login_time el "
             . "JOIN stoma_employee e ON e.employeeid = el.employeeid "
             . "LEFT JOIN stoma_week_roster wr ON wr.storeid = el.storeid AND wr.employeeid = el.employeeid AND wr.weekid = el.weekid AND wr.day = el.day "
             . "WHERE el.storeid = ? AND e.status != 'Deactivate'";
        
        $params = [$storeid];
        
        // Date filtering (like CI line 860-864)
        if ($enddate) {
            $sql .= " AND (Date(el.clockin) >= ? AND Date(el.clockin) <= ?)";
            $params[] = $date;
            $params[] = $enddate;
        } else {
            $sql .= " AND (Date(el.clockin) = ? OR Date(el.clockout) = ?)";
            $params[] = $date;
            $params[] = $date;
        }
        
        // Employee filtering (like CI line 865-867)
        if (!empty($employeeids)) {
            $placeholders = implode(',', array_fill(0, count($employeeids), '?'));
            $sql .= " AND el.employeeid IN ($placeholders)";
            $params = array_merge($params, $employeeids);
        }
        
        // Group by weekid, day, and employeeid to match data output and satisfy MySQL strict mode
        // This ensures one row per employee per day (like the user's data shows)
        $sql .= " GROUP BY el.weekid, el.day, el.employeeid, e.firstname, e.lastname, el.status, wr.start_time, wr.end_time, el.storeid ORDER BY el.clockin DESC";
        
        // Execute raw query for maximum speed
        $results = DB::select($sql, $params);
        
        // Process results minimally - just format what's needed
        $processedResults = [];
        foreach ($results as $item) {
            $clockin = $item->clockin ?? null;
            $clockout = $item->clockout ?? null;
            
            // Calculate total hours (like CI)
            $total = 0;
            if ($clockin && $clockout && ($item->status ?? '') !== 'clockout') {
                $totalMinutes = (int)($item->timediff ?? 0);
                if ($totalMinutes > 0) {
                    $total = round($totalMinutes / 60, 2);
                }
            }
            
            // Format roster times
            $rosterStart = $item->roster_start_time ?? '00:00';
            $rosterEnd = $item->roster_end_time ?? '00:00';
            
            // Return as array (matches CI's result_array format)
            $processedResults[] = [
                'eltid' => $item->eltid ?? null,
                'employeeid' => $item->employeeid ?? null,
                'firstname' => $item->firstname ?? '',
                'lastname' => $item->lastname ?? '',
                'weekid' => $item->weekid ?? null,
                'day' => $item->day ?? '',
                'status' => $item->status ?? '',
                'clockin' => $clockin,
                'clockout' => $clockout,
                'roster_start_time' => $rosterStart,
                'roster_end_time' => $rosterEnd,
                'total' => $total,
                'storeid' => $item->storeid ?? null,
            ];
        }
        
        return collect($processedResults);
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
     * Get week number by weekid (matches CI's get_week_by_id).
     */
    public function getWeekById($weekid)
    {
        $week = DB::table('stoma_week')
            ->where('weekid', $weekid)
            ->value('weeknumber');
        
        return $week ?? 0;
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

    /**
     * Get week ID from week number and year.
     */
    public function getWeekId($weekNumber, $year)
    {
        $yearModel = Year::where('year', (string)$year)->first();
        if (!$yearModel) {
            return null;
        }

        $weekModel = Week::where('weeknumber', $weekNumber)
            ->where('yearid', $yearModel->yearid)
            ->first();

        return $weekModel ? $weekModel->weekid : null;
    }

    /**
     * Get start and end date for a week number and year.
     */
    public function getStartAndEndDate($week, $year)
    {
        $dto = new \DateTime();
        $dto->setISODate($year, $week);
        $weekStart = $dto->format('Y-m-d');
        $dto->modify('+6 days');
        $weekEnd = $dto->format('Y-m-d');
        
        return [
            'week_start' => $weekStart,
            'week_end' => $weekEnd,
        ];
    }

    /**
     * Get employee week clock time data.
     */
    public function getEmployeeWeekDataByEmployee($storeid, $employeeid, $weekid, $weekStartDate, $weekEndDate)
    {
        $clockDetails = EmpLoginTime::with(['employee', 'week'])
            ->where('storeid', $storeid)
            ->where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->where('inRoster', 'Yes')
            ->whereBetween(DB::raw('Date(clockin)'), [$weekStartDate, $weekEndDate])
            ->orderBy('clockin', 'ASC')
            ->get();

        $totalPayrolHr = 0;
        $totalBreakout = 0;

        foreach ($clockDetails as $key => $val) {
            $rosterData = $this->getRosterHour($val->employeeid, $val->weekid, $val->day);
            
            if (!empty($rosterData) && $rosterData['start_time'] !== '00:00') {
                $rosterStartTime = $rosterData['start_time'] . ':00';
                $rosterEndTime = $rosterData['end_time'] . ':00';
            } else {
                $rosterStartTime = "0";
                $rosterEndTime = "0";
            }

            $total = $this->calculateStartTime($val, $rosterData);
            
            $employee = StoreEmployee::find($val->employeeid);
            if ($employee && $employee->paid_break === 'No') {
                $totalBreakout = ($employee->break_every_hrs * $total) * $employee->break_min;
            }

            $totalFinal = ($total * 60) - $totalBreakout;
            $total = number_format($totalFinal / 60, 2);

            $val->roster_start_time = $rosterStartTime;
            $val->roster_end_time = $rosterEndTime;
            $val->total = (float)$total;
            $val->totalBreakout = $totalBreakout;
            $totalPayrolHr += (float)$total;
        }

        return [
            'clockdetails' => $clockDetails,
            'totalPayrol' => $totalPayrolHr,
        ];
    }

    /**
     * Get all employees week clock time data for a specific week.
     */
    public function getAllEmployeeWeekDataByWeek($storeid, $weekid, $weekStartDate, $weekEndDate)
    {
        $clockDetails = EmpLoginTime::with(['employee', 'week'])
            ->where('storeid', $storeid)
            ->where('weekid', $weekid)
            ->where('inRoster', 'Yes')
            ->whereBetween(DB::raw('Date(clockin)'), [$weekStartDate, $weekEndDate])
            ->whereHas('employee', function($q) {
                $q->where('status', '!=', 'Deactivate');
            })
            ->orderBy('clockin', 'ASC')
            ->get();

        $totalPayrolHr = 0;

        foreach ($clockDetails as $key => $val) {
            $rosterData = $this->getRosterHour($val->employeeid, $val->weekid, $val->day);
            
            if (!empty($rosterData) && isset($rosterData['start_time']) && $rosterData['start_time'] !== '00:00') {
                $rosterStartTime = $rosterData['start_time'];
                $rosterEndTime = $rosterData['end_time'];
            } else {
                $rosterStartTime = "0";
                $rosterEndTime = "0";
            }

            $total = $this->calculateStartTime($val, $rosterData);
            
            // Reset totalBreakout for each entry
            $totalBreakout = 0;
            
            $employee = StoreEmployee::find($val->employeeid);
            if ($employee && $employee->paid_break === 'No' && $total > 0 && $employee->break_every_hrs > 0 && $employee->break_min > 0) {
                // Match CI calculation exactly: (break_every_hrs * total) * break_min
                $totalBreakout = ($employee->break_every_hrs * $total) * $employee->break_min;
            }

            $totalFinal = ($total * 60) - $totalBreakout;
            $total = $totalFinal > 0 ? number_format($totalFinal / 60, 2) : 0;

            $val->roster_start_time = $rosterStartTime;
            $val->roster_end_time = $rosterEndTime;
            $val->total = (float)$total;
            $val->totalBreakout = (int)$totalBreakout;
            $totalPayrolHr += (float)$total;
        }

        return [
            'clockdetails' => $clockDetails,
            'totalPayrol' => $totalPayrolHr,
        ];
    }

    /**
     * Calculate start time for hours calculation.
     */
    protected function calculateStartTime($data, $rosterData)
    {
        if (!empty($rosterData) && $rosterData['start_time'] !== '00:00') {
            $rosterStartTime = $rosterData['start_time'] . ':00';
        } else {
            $rosterStartTime = "0";
        }

        $startTime = "";
        if (intval($rosterStartTime) == 0 || $rosterStartTime === "0") {
            $startTime = $data->clockin;
        } else {
            $clockinDate = Carbon::parse($data->clockin)->format('Y-m-d');
            $startTime = $clockinDate . ' ' . $rosterStartTime;
            $rosterDate = Carbon::parse($startTime);
            $clockDate = Carbon::parse($data->clockin);
            
            if ($clockDate->gt($rosterDate)) {
                $startTime = $data->clockin;
            }
        }

        // Calculate hours between two datetime strings
        if (!$startTime || !$data->clockout) {
            return 0;
        }

        $start = Carbon::parse($startTime);
        $end = Carbon::parse($data->clockout);
        
        return round($end->diffInMinutes($start) / 60, 2);
    }

    /**
     * Get all yearly hours for a specific employee (all years grouped).
     */
    public function getAllYearlyHrsByEmployee($storeid, $employeeid)
    {
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
                DB::raw('AVG(CAST(ep.hours_worked AS DECIMAL(10,2)) / 5) AS fourmonthavg_hrs'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('e.employeeid', $employeeid)
            ->where('ep.year', '>=', $oneYearAgo->year)
            ->where('ep.year', '<=', Carbon::now()->year)
            ->groupBy('ep.year', 'ep.employeeid', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->orderBy('ep.year', 'DESC')
            ->get();
        
        return $results;
    }

    /**
     * Get all weekly hours for a specific employee (all weeks grouped).
     */
    public function getAllWeekHrsByEmployee($storeid, $employeeid)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.*',
                'e.firstname',
                'e.lastname',
                'e.sallary_method',
                DB::raw('CAST(ep.hours_worked AS DECIMAL(10,2)) AS hours_worked'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('e.employeeid', $employeeid)
            ->groupBy('ep.storeid', 'ep.employeeid', 'ep.weekno', 'ep.year', 'ep.payroll_id', 'ep.week_start', 'ep.week_end', 'ep.hours_worked', 'ep.numberofdaysworked', 'ep.break_deducted', 'ep.sunday_hrs', 'ep.owertime1_hrs', 'ep.owertime2_hrs', 'ep.holiday_hrs', 'ep.holiday_days', 'ep.sickpay_hrs', 'ep.extras1_hrs', 'ep.extras2_hrs', 'ep.total_hours', 'ep.notes', 'ep.insertdate', 'ep.insertip', 'ep.editdate', 'ep.editip', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->orderBy('ep.week_start', 'DESC')
            ->orderBy('ep.year', 'DESC')
            ->get();
    }

    /**
     * Get all weekly hours for all employees in a specific week and year.
     */
    public function getAllWeekHrsByWeek($storeid, $weekno, $year)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.*',
                'e.firstname',
                'e.lastname',
                'e.sallary_method',
                DB::raw('CAST(ep.hours_worked AS DECIMAL(10,2)) AS hours_worked'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('ep.weekno', $weekno)
            ->where('ep.year', $year)
            ->where('e.status', '!=', 'Deactivate')
            ->groupBy('ep.storeid', 'ep.employeeid', 'ep.weekno', 'ep.year', 'ep.payroll_id', 'ep.week_start', 'ep.week_end', 'ep.hours_worked', 'ep.numberofdaysworked', 'ep.break_deducted', 'ep.sunday_hrs', 'ep.owertime1_hrs', 'ep.owertime2_hrs', 'ep.holiday_hrs', 'ep.holiday_days', 'ep.sickpay_hrs', 'ep.extras1_hrs', 'ep.extras2_hrs', 'ep.total_hours', 'ep.notes', 'ep.insertdate', 'ep.insertip', 'ep.editdate', 'ep.editip', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->get();
    }

    /**
     * Get all yearly hours for all employees in a specific year.
     */
    public function getAllYearlyHrsAllEmployee($storeid, $year)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
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
            ->where('ep.year', $year)
            ->where('e.status', '!=', 'Deactivate')
            ->groupBy('ep.year', 'ep.employeeid', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->orderBy('ep.year', 'DESC')
            ->get();
    }

    /**
     * Get yearly hours breakdown for an employee in a specific year (weekly breakdown).
     */
    public function getYearlyHrsByEmployee($storeid, $employeeid, $year)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.*',
                'e.firstname',
                'e.lastname',
                'e.sallary_method',
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2))) AS hours_worked'),
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('e.employeeid', $employeeid)
            ->where('ep.year', $year)
            ->groupBy('ep.payroll_id', 'ep.employeeid', 'ep.weekno', 'ep.year', 'ep.week_start', 'ep.week_end', 'ep.hours_worked', 'ep.break_deducted', 'ep.sunday_hrs', 'ep.owertime1_hrs', 'ep.owertime2_hrs', 'ep.holiday_hrs', 'ep.holiday_days', 'ep.sickpay_hrs', 'ep.extras1_hrs', 'ep.extras2_hrs', 'ep.total_hours', 'ep.notes', 'ep.insertdate', 'ep.insertip', 'ep.editdate', 'ep.editip', 'e.firstname', 'e.lastname', 'e.sallary_method')
            ->orderBy('ep.payroll_id', 'ASC')
            ->get();
    }

    /**
     * Get a single payroll hour record by payroll_id.
     */
    public function getPayrollHourById(int $storeid, int $payrollId)
    {
        return DB::table('stoma_emp_payroll_hrs as ep')
            ->select([
                'ep.*',
                'e.firstname',
                'e.lastname',
                'e.sallary_method',
            ])
            ->leftJoin('stoma_employee as e', 'ep.employeeid', '=', 'e.employeeid')
            ->where('ep.storeid', $storeid)
            ->where('ep.payroll_id', $payrollId)
            ->first();
    }
}

