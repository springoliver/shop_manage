<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Roster;
use App\Models\WeekRoster;
use App\Models\StoreEmployee;
use App\Models\Department;
use App\Services\StoreOwner\RosterService;
use App\Services\StoreOwner\ModuleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RosterController extends Controller
{
    protected RosterService $rosterService;
    protected ModuleService $moduleService;

    public function __construct(RosterService $rosterService, ModuleService $moduleService)
    {
        $this->rosterService = $rosterService;
        $this->moduleService = $moduleService;
    }

    /**
     * Check if Roster module is installed.
     * Redirects to module settings if not installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Roster')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of base rosters.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get employees without rosters
        $employees = $this->rosterService->getEmployeesWithoutRoster($storeid);
        
        // Get all base rosters
        $weekroster = $this->rosterService->getAllRosters($storeid);
        
        // Get unique employees who have rosters
        $employeedata = $weekroster->unique('employeeid')
            ->map(function ($roster) {
                return $roster->employee;
            })
            ->filter()
            ->values();
        
        // Get departments
        $departments = Department::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.roster.index', compact('employees', 'weekroster', 'employeedata', 'departments'));
    }

    /**
     * Display rosters filtered by department.
     */
    public function indexDept(string $departmentid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $departmentid = base64_decode($departmentid);
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get employees without rosters
        $employees = $this->rosterService->getEmployeesWithoutRoster($storeid);
        
        // Get rosters by department
        $weekroster = $this->rosterService->getRostersByDepartment($storeid, $departmentid);
        
        // Get unique employees who have rosters
        $employeedata = $weekroster->unique('employeeid')
            ->map(function ($roster) {
                return $roster->employee;
            })
            ->filter()
            ->values();
        
        // Get departments
        $departments = Department::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.roster.index_dept', compact('employees', 'weekroster', 'employeedata', 'departments', 'departmentid'));
    }

    /**
     * Show the form for creating a base roster.
     */
    public function create(string $employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        // Get existing roster if any
        $roster = Roster::where('employeeid', $employeeid)
            ->where('storeid', $employee->storeid)
            ->first();
        
        return view('storeowner.roster.create', compact('employee', 'roster'));
    }

    /**
     * Store a newly created base roster.
     */
    public function store(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $validated = $request->validate([
            'employeeid' => 'required|integer',
            'Sunday_start' => 'required',
            'Sunday_end' => 'required',
            'Monday_start' => 'required',
            'Monday_end' => 'required',
            'Tuesday_start' => 'required',
            'Tuesday_end' => 'required',
            'Wednesday_start' => 'required',
            'Wednesday_end' => 'required',
            'Thursday_start' => 'required',
            'Thursday_end' => 'required',
            'Friday_start' => 'required',
            'Friday_end' => 'required',
            'Saturday_start' => 'required',
            'Saturday_end' => 'required',
        ]);
        
        $employee = StoreEmployee::findOrFail($validated['employeeid']);
        
        // Delete existing roster for this employee
        Roster::where('employeeid', $employee->employeeid)
            ->where('storeid', $storeid)
            ->delete();
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $day) {
            $startKey = $day . '_start';
            $endKey = $day . '_end';
            $startTime = $validated[$startKey];
            $endTime = $validated[$endKey];
            
            $workStatus = ($startTime === 'off' || $endTime === 'off') ? 'off' : 'on';
            
            Roster::create([
                'storeid' => $storeid,
                'employeeid' => $employee->employeeid,
                'departmentid' => $employee->departmentid,
                'start_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($startTime)),
                'end_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($endTime)),
                'day' => $day,
                'shift' => 'day', // Default shift
                'work_status' => $workStatus,
                'insertdatetime' => now(),
                'insertip' => $request->ip(),
                'status' => 'current',
                'break_every_hrs' => $employee->break_every_hrs ?? 0,
                'break_min' => $employee->break_min ?? 0,
                'paid_break' => $employee->paid_break ?? 'Yes',
            ]);
        }
        
        return redirect()->route('storeowner.roster.index')
            ->with('success', 'Roster created successfully.');
    }

    /**
     * Show the form for editing a base roster.
     */
    public function edit(string $employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get existing roster
        $rosters = Roster::where('employeeid', $employeeid)
            ->where('storeid', $storeid)
            ->get()
            ->keyBy('day');
        
        return view('storeowner.roster.edit', compact('employee', 'rosters'));
    }

    /**
     * Update the base roster.
     */
    public function update(Request $request, string $employeeid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $validated = $request->validate([
            'Sunday_start' => 'required',
            'Sunday_end' => 'required',
            'Monday_start' => 'required',
            'Monday_end' => 'required',
            'Tuesday_start' => 'required',
            'Tuesday_end' => 'required',
            'Wednesday_start' => 'required',
            'Wednesday_end' => 'required',
            'Thursday_start' => 'required',
            'Thursday_end' => 'required',
            'Friday_start' => 'required',
            'Friday_end' => 'required',
            'Saturday_start' => 'required',
            'Saturday_end' => 'required',
        ]);
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $day) {
            $startKey = $day . '_start';
            $endKey = $day . '_end';
            $startTime = $validated[$startKey];
            $endTime = $validated[$endKey];
            
            $workStatus = ($startTime === 'off' || $endTime === 'off') ? 'off' : 'on';
            
            Roster::updateOrCreate(
                [
                    'storeid' => $storeid,
                    'employeeid' => $employee->employeeid,
                    'day' => $day,
                ],
                [
                    'departmentid' => $employee->departmentid,
                    'start_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($startTime)),
                    'end_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($endTime)),
                    'shift' => 'day',
                    'work_status' => $workStatus,
                    'editdatetime' => now(),
                    'editip' => $request->ip(),
                    'status' => 'current',
                    'break_every_hrs' => $employee->break_every_hrs ?? 0,
                    'break_min' => $employee->break_min ?? 0,
                    'paid_break' => $employee->paid_break ?? 'Yes',
                ]
            );
        }
        
        return redirect()->route('storeowner.roster.index')
            ->with('success', 'Roster updated successfully.');
    }

    /**
     * Remove the base roster.
     */
    public function destroy(string $employeeid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        Roster::where('employeeid', $employee->employeeid)
            ->where('storeid', $storeid)
            ->delete();
        
        return redirect()->route('storeowner.roster.index')
            ->with('success', 'Roster deleted successfully.');
    }

    /**
     * View employee's base roster.
     */
    public function view(string $employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $rosters = Roster::where('employeeid', $employee->employeeid)
            ->where('storeid', $storeid)
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
            ->get();
        
        return view('storeowner.roster.view', compact('employee', 'rosters'));
    }

    /**
     * Display weekly rosters.
     */
    public function weekroster(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all base rosters to show employees
        $weekroster = $this->rosterService->getAllRosters($storeid);
        
        // Get unique employees grouped by employeeid
        $employees = collect();
        $employeeIds = $weekroster->pluck('employeeid')->unique();
        
        foreach ($employeeIds as $employeeId) {
            $firstRoster = $weekroster->where('employeeid', $employeeId)->first();
            if ($firstRoster && $firstRoster->employee) {
                $employees->push([$firstRoster->employee]);
            }
        }
        
        return view('storeowner.roster.weekroster', compact('weekroster', 'employees'));
    }

    /**
     * Generate weekly roster from base roster.
     */
    public function weekrosteradd(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'weeknumber' => 'required|date',
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Parse week number and year from date
        $date = new \DateTime($validated['weeknumber']);
        $weekNumber = (int) $date->format('W');
        $year = $date->format('Y');
        
        // Get leave requests if available (for future integration)
        $leaveRequests = []; // TODO: Integrate with holiday_request module
        
        // Generate weekly roster
        $this->rosterService->generateWeeklyRoster($storeid, $weekNumber, $year, $leaveRequests);
        
        return redirect()->route('storeowner.roster.weekroster')
            ->with('success', 'Roster added successfully.');
    }

    /**
     * Edit weekly roster for a specific employee and week.
     */
    public function editweekroster(string $employeeid, string $weekid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $weekid = (int) base64_decode($weekid);
        
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get week roster entries for this employee and week
        $weekRosters = WeekRoster::where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->where('storeid', $storeid)
            ->get()
            ->keyBy('day');
        
        $week = \App\Models\Week::find($weekid);
        
        return view('storeowner.roster.editweekroster', compact('employee', 'weekRosters', 'week'));
    }

    /**
     * Update weekly roster.
     */
    public function updateweekroster(Request $request, string $employeeid, string $weekid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $weekid = (int) base64_decode($weekid);
        
        $employee = StoreEmployee::findOrFail($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $validated = $request->validate([
            'Sunday_start' => 'required',
            'Sunday_end' => 'required',
            'Monday_start' => 'required',
            'Monday_end' => 'required',
            'Tuesday_start' => 'required',
            'Tuesday_end' => 'required',
            'Wednesday_start' => 'required',
            'Wednesday_end' => 'required',
            'Thursday_start' => 'required',
            'Thursday_end' => 'required',
            'Friday_start' => 'required',
            'Friday_end' => 'required',
            'Saturday_start' => 'required',
            'Saturday_end' => 'required',
        ]);
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $day) {
            $startKey = $day . '_start';
            $endKey = $day . '_end';
            $startTime = $validated[$startKey];
            $endTime = $validated[$endKey];
            
            $weekRoster = WeekRoster::where('employeeid', $employeeid)
                ->where('weekid', $weekid)
                ->where('storeid', $storeid)
                ->where('day', $day)
                ->first();
            
            if ($weekRoster) {
                $workStatus = ($startTime === 'off' || $endTime === 'off') ? 'off' : 'on';
                
                $weekRoster->update([
                    'start_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($startTime)),
                    'end_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($endTime)),
                    'work_status' => $workStatus,
                    'editdatetime' => now(),
                    'break_every_hrs' => $employee->break_every_hrs ?? 1,
                    'break_min' => $employee->break_min ?? 4,
                    'paid_break' => $employee->paid_break ?? 'Yes',
                ]);
                
                // TODO: Update emp_payroll entry with calculated hours
            }
        }
        
        return redirect()->route('storeowner.roster.viewweekroster', ['weekid' => base64_encode($weekid)])
            ->with('success', 'Weekly roster updated successfully.');
    }

    /**
     * View weekly roster for a specific week.
     */
    public function viewweekroster(string $weekid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $weekid = (int) base64_decode($weekid);
        $week = \App\Models\Week::findOrFail($weekid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $weekRosters = $this->rosterService->getWeekRosters($storeid, $weekid);
        
        // Group by employee
        $rostersByEmployee = $weekRosters->groupBy('employeeid');
        
        return view('storeowner.roster.viewweekroster', compact('week', 'rostersByEmployee', 'weekid'));
    }

    /**
     * Delete weekly roster for an employee.
     */
    public function deleterosterweek(string $weekid, string $employeeid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $weekid = (int) base64_decode($weekid);
        $employeeid = base64_decode($employeeid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        WeekRoster::where('wrid', $weekid)
            ->where('employeeid', $employeeid)
            ->where('storeid', $storeid)
            ->delete();
        
        return redirect()->back()
            ->with('success', 'Roster deleted successfully.');
    }

    /**
     * Display roster for a specific week (with optional department filter).
     */
    public function rosterforweek(string $weekid, ?string $departmentid = null): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $weekid = (int) base64_decode($weekid);
        $week = \App\Models\Week::findOrFail($weekid);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $query = WeekRoster::where('storeid', $storeid)
            ->where('weekid', $weekid)
            ->with(['employee' => function ($q) {
                $q->where('status', '!=', 'Deactivate');
            }])
            ->whereHas('employee', function ($q) {
                $q->where('status', '!=', 'Deactivate');
            });
        
        if ($departmentid) {
            $departmentid = base64_decode($departmentid);
            $query->where('departmentid', $departmentid);
        }
        
        $weekRosters = $query->get();
        
        $rostersByEmployee = $weekRosters->groupBy('employeeid');
        
        $departments = Department::where('storeid', $storeid)
            ->where('status', 'Enable')
            ->get();
        
        return view('storeowner.roster.rosterforweek', compact('week', 'rostersByEmployee', 'departments', 'departmentid'));
    }
}

