<?php

namespace App\Services\StoreOwner;

use App\Models\Year;
use App\Models\Week;
use App\Models\Roster;
use App\Models\WeekRoster;
use App\Models\StoreEmployee;
use App\Models\Department;
use Carbon\Carbon;

class RosterService
{
    /**
     * Get or create year entry.
     *
     * @param string $year
     * @return Year
     */
    public function getOrCreateYear(string $year): Year
    {
        $yearModel = Year::where('year', $year)->first();
        
        if (!$yearModel) {
            $yearModel = Year::create(['year' => $year]);
        }
        
        return $yearModel;
    }

    /**
     * Get or create week entry.
     *
     * @param int $weekNumber
     * @param int $yearId
     * @return Week
     */
    public function getOrCreateWeek(int $weekNumber, int $yearId): Week
    {
        $week = Week::where('weeknumber', $weekNumber)
            ->where('yearid', $yearId)
            ->first();
        
        if (!$week) {
            $week = Week::create([
                'weeknumber' => $weekNumber,
                'yearid' => $yearId,
            ]);
        }
        
        return $week;
    }

    /**
     * Calculate dates for each day of a week.
     *
     * @param string $year
     * @param int $weekNumber
     * @return array
     */
    public function calculateWeekDates(string $year, int $weekNumber): array
    {
        $dates = [];
        
        // Create a date for Monday of the ISO week
        $mondayDate = Carbon::now()
            ->setISODate((int) $year, $weekNumber, 1); // ISO week starts on Monday
        
        // Calculate Sunday (day before Monday) for the week
        $sundayDate = $mondayDate->copy()->subDay();
        
        // Build dates array from Sunday to Saturday
        $dayOrder = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $currentDate = $sundayDate->copy();
        
        foreach ($dayOrder as $dayName) {
            $dates[$dayName] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }
        
        return $dates;
    }

    /**
     * Calculate total hours with break deduction.
     *
     * @param string $startTime
     * @param string $endTime
     * @param int $breakEveryHrs
     * @param int $breakMin
     * @param string $paidBreak ('Yes' or 'No')
     * @return float
     */
    public function calculateHours(string $startTime, string $endTime, int $breakEveryHrs, int $breakMin, string $paidBreak): float
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        $diff = $end->diffInHours($start) + ($end->diffInMinutes($start) % 60) / 60;
        
        // If unpaid breaks and hours exceed break threshold
        if ($diff > $breakEveryHrs && $paidBreak === 'No') {
            $noBreak = ceil($diff / 2) - 1;
            $breakHours = $noBreak * ($breakMin / 60);
            $diff = abs($diff - $breakHours);
        }
        
        return round($diff, 2);
    }

    /**
     * Get roster status based on current week.
     *
     * @param int $weekNumber
     * @return string ('past', 'current', 'future')
     */
    public function getRosterStatus(int $weekNumber): string
    {
        $currentWeek = (int) date('W');
        
        if ($weekNumber < $currentWeek) {
            return 'past';
        } elseif ($weekNumber == $currentWeek) {
            return 'current';
        } else {
            return 'future';
        }
    }

    /**
     * Get employees who have rosters.
     *
     * @param int $storeid
     * @return array
     */
    public function getRosterEmployees(int $storeid): array
    {
        return Roster::where('storeid', $storeid)
            ->distinct()
            ->pluck('employeeid')
            ->toArray();
    }

    /**
     * Get employees without rosters.
     *
     * @param int $storeid
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEmployeesWithoutRoster(int $storeid)
    {
        $rosterEmployeeIds = $this->getRosterEmployees($storeid);
        
        return StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->whereNotIn('employeeid', $rosterEmployeeIds)
            ->orderBy('departmentid', 'asc')
            ->get();
    }

    /**
     * Get all base rosters for a store.
     *
     * @param int $storeid
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllRosters(int $storeid)
    {
        // Order by day to match CI behavior (Sunday=0, Monday=1, etc.)
        $dayOrder = "CASE 
            WHEN day = 'Sunday' THEN 0
            WHEN day = 'Monday' THEN 1
            WHEN day = 'Tuesday' THEN 2
            WHEN day = 'Wednesday' THEN 3
            WHEN day = 'Thursday' THEN 4
            WHEN day = 'Friday' THEN 5
            WHEN day = 'Saturday' THEN 6
            ELSE 99
        END";
        
        return Roster::where('storeid', $storeid)
            ->with(['employee' => function ($query) {
                $query->where('status', '!=', 'Deactivate');
            }])
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Deactivate');
            })
            ->orderByRaw($dayOrder)
            ->get();
    }

    /**
     * Get rosters filtered by department.
     *
     * @param int $storeid
     * @param int $departmentid
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRostersByDepartment(int $storeid, int $departmentid)
    {
        return Roster::where('storeid', $storeid)
            ->with(['employee' => function ($query) use ($departmentid) {
                $query->where('status', '!=', 'Deactivate')
                      ->where('departmentid', $departmentid);
            }])
            ->whereHas('employee', function ($query) use ($departmentid) {
                $query->where('status', '!=', 'Deactivate')
                      ->where('departmentid', $departmentid);
            })
            ->get();
    }

    /**
     * Get week rosters for a specific week.
     *
     * @param int $storeid
     * @param int $weekid
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWeekRosters(int $storeid, int $weekid)
    {
        return WeekRoster::where('storeid', $storeid)
            ->where('weekid', $weekid)
            ->with(['employee' => function ($query) {
                $query->where('status', '!=', 'Deactivate');
            }])
            ->whereHas('employee', function ($query) {
                $query->where('status', '!=', 'Deactivate');
            })
            ->get();
    }

    /**
     * Generate weekly roster from base roster.
     *
     * @param int $storeid
     * @param int $weekNumber
     * @param string $year
     * @param array $leaveRequests Optional array of leave requests
     * @return void
     */
    public function generateWeeklyRoster(int $storeid, int $weekNumber, string $year, array $leaveRequests = []): void
    {
        // Get or create year and week
        $yearModel = $this->getOrCreateYear($year);
        $week = $this->getOrCreateWeek($weekNumber, $yearModel->yearid);
        
        // Delete existing week roster if exists
        WeekRoster::where('storeid', $storeid)
            ->where('weekid', $week->weekid)
            ->delete();
        
        // Get roster status
        $status = $this->getRosterStatus($weekNumber);
        
        // Calculate dates for the week
        $dates = $this->calculateWeekDates($year, $weekNumber);
        
        // Get all base rosters for the store
        $baseRosters = Roster::where('storeid', $storeid)->get();
        
        // Group by employee
        $rostersByEmployee = $baseRosters->groupBy('employeeid');
        
        foreach ($rostersByEmployee as $employeeId => $rosters) {
            $employee = StoreEmployee::find($employeeId);
            if (!$employee) {
                continue;
            }
            
            // Check if employee is on leave for this week
            $employeeLeaveDates = [];
            foreach ($leaveRequests as $leave) {
                if ($leave['employeeid'] == $employeeId && $leave['status'] == 'Approved') {
                    $startDate = Carbon::parse($leave['from_date']);
                    $endDate = Carbon::parse($leave['to_date']);
                    
                    for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                        $employeeLeaveDates[] = $date->format('Y-m-d');
                    }
                }
            }
            
            foreach ($rosters as $roster) {
                $dayDate = $dates[$roster->day] ?? null;
                
                if (!$dayDate) {
                    continue;
                }
                
                // Check if employee is on leave for this date
                $workStatus = in_array($dayDate, $employeeLeaveDates) ? 'off' : $roster->work_status;
                
                // Create week roster entry
                $weekRoster = WeekRoster::create([
                    'storeid' => $storeid,
                    'employeeid' => $employeeId,
                    'departmentid' => $roster->departmentid ?? $employee->departmentid,
                    'start_time' => $workStatus === 'off' ? '00:00:00' : $roster->start_time,
                    'end_time' => $workStatus === 'off' ? '00:00:00' : $roster->end_time,
                    'day' => $roster->day,
                    'shift' => $roster->shift,
                    'day_date' => $dayDate,
                    'work_status' => $workStatus,
                    'weekid' => $week->weekid,
                    'status' => $status,
                    'insertdatetime' => now(),
                    'insertip' => request()->ip(),
                    'break_every_hrs' => $roster->break_every_hrs,
                    'break_min' => $roster->break_min,
                    'paid_break' => $roster->paid_break,
                ]);
                
                // Calculate hours for payroll (if work_status is 'on')
                if ($workStatus === 'on') {
                    $totalHours = $this->calculateHours(
                        $roster->start_time,
                        $roster->end_time,
                        $roster->break_every_hrs,
                        $roster->break_min,
                        $roster->paid_break
                    );
                } else {
                    $totalHours = 0;
                }
                
                // TODO: Create emp_payroll entry if needed
                // This will be handled when we integrate with Employee Payroll module
            }
        }
    }
}

