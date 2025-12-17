<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Roster;
use App\Models\WeekRoster;
use App\Models\Week;
use App\Models\Year;
use App\Models\StoreEmployee;
use App\Models\Department;
use App\Services\StoreOwner\RosterService;
use App\Services\StoreOwner\ModuleService;
use App\Http\StoreOwner\Traits\HandlesEmployeeAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RosterController extends Controller
{
    use HandlesEmployeeAccess;
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
     * Handles both storeowner and employee guards.
     */
    protected function checkModuleAccess()
    {
        $storeid = $this->getStoreId();
        
        if (!$storeid) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Store not found');
        }
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Roster')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }
    

    /**
     * Display a listing of base rosters.
     */
    public function index(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        // Get employees without rosters
        $employees = $this->rosterService->getEmployeesWithoutRoster($storeid);
        
        // Get all unique employees who have rosters (for client-side pagination)
        $employeedata = DB::table('stoma_roster as r')
            ->join('stoma_employee as e', 'e.employeeid', '=', 'r.employeeid')
            ->where('r.storeid', $storeid)
            ->where('e.status', '!=', 'Deactivate')
            ->select('e.employeeid', 'e.firstname', 'e.lastname')
            ->distinct()
            ->orderBy('e.firstname', 'asc')
            ->get()
            ->map(function($item) {
                return (object) [
                    'employeeid' => $item->employeeid,
                    'firstname' => $item->firstname,
                    'lastname' => $item->lastname,
                ];
            });
        
        // Get employee IDs
        $employeeIds = $employeedata->pluck('employeeid')->toArray();
        
        // Get all rosters for these employees
        $weekroster = Roster::where('storeid', $storeid)
            ->whereIn('employeeid', $employeeIds)
            ->with(['employee' => function ($query) {
                $query->where('status', '!=', 'Deactivate');
            }])
            ->get();
        
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
        $storeid = $this->getStoreId();
        
        // Get employees without rosters (for Add Roster form dropdown)
        // Match CI behavior: show ALL employees without rosters (not filtered by department)
        // This matches Roster Template behavior
        $employeesForForm = $this->rosterService->getEmployeesWithoutRoster($storeid)
            ->map(function($emp) {
                return (object) [
                    'employeeid' => $emp->employeeid,
                    'firstname' => $emp->firstname,
                    'lastname' => $emp->lastname,
                    'roster_week_hrs' => $emp->roster_week_hrs ?? 0,
                    'roster_day_hrs' => $emp->roster_day_hrs ?? 0,
                ];
            });
        
        // Get employees without rosters (for service method)
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
        
        return view('storeowner.roster.index_dept', compact('employees', 'employeesForForm', 'weekroster', 'employeedata', 'departments', 'departmentid'));
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
        // Get all base rosters to show employees
        $weekroster = $this->rosterService->getAllRosters($storeid);
        
        // Get unique employees grouped by employeeid
        $employees = collect();
        $employeeIds = $weekroster->pluck('employeeid')->unique();
        
        foreach ($employeeIds as $employeeId) {
            $firstRoster = $weekroster->where('employeeid', $employeeId)->first();
            if ($firstRoster && $firstRoster->employee) {
                $employees->push($firstRoster->employee);
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
        
        $storeid = $this->getStoreId();
        
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
     * View weekly roster for a specific week (or current week if no weekid provided).
     */
    public function viewweekroster(?string $weekid = null): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        if ($weekid) {
            $weekid = (int) base64_decode($weekid);
            $week = Week::findOrFail($weekid);
        } else {
            // Get current week
            $date = date('Y-m-d');
            $weeknumber = (int) date('W');
            $year = (int) date('Y');
            
            $yearModel = Year::where('year', $year)->first();
            if ($yearModel) {
                $week = Week::where('weeknumber', $weeknumber)
                    ->where('yearid', $yearModel->yearid)
                    ->first();
            } else {
                $week = null;
            }
            
            if (!$week) {
                return redirect()->route('storeowner.roster.weekroster')
                    ->with('error', 'No roster found for current week. Please generate a week roster first.');
            }
            
            $weekid = $week->weekid;
        }
        
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
        
        $storeid = $this->getStoreId();
        
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
        
        $storeid = $this->getStoreId();
        
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

    /**
     * Print view of current week's roster.
     */
    public function printviewroster(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $date = date('Y-m-d');
        $weeknumber = (int) date('W');
        $year = (int) date('Y');
        
        $yearModel = Year::where('year', $year)->first();
        if ($yearModel) {
            $week = Week::where('weeknumber', $weeknumber)
                ->where('yearid', $yearModel->yearid)
                ->first();
        } else {
            $week = null;
        }
        
        $weekRosters = collect();
        $employees = collect();
        
        if ($week) {
            $weekRosters = WeekRoster::where('storeid', $storeid)
                ->where('weekid', $week->weekid)
                ->with(['employee' => function($q) {
                    $q->where('status', '!=', 'Deactivate');
                }])
                ->whereHas('employee', function($q) {
                    $q->where('status', '!=', 'Deactivate');
                })
                ->get();
            
            $employeeIds = $weekRosters->pluck('employeeid')->unique();
            $employees = StoreEmployee::whereIn('employeeid', $employeeIds)
                ->where('status', 'Active')
                ->get();
        }
        
        $rostersByEmployee = $weekRosters->groupBy('employeeid');
        
        return view('storeowner.roster.printviewroster', compact('weeknumber', 'year', 'rostersByEmployee', 'employees', 'date'));
    }

    /**
     * Search and print roster for a specific week.
     */
    public function searchprintroster(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        // Handle GET request (show initial form)
        if ($request->isMethod('GET')) {
            $weeknumber = 0;
            $year = (int) date('Y');
            $rostersByEmployee = collect();
            $employees = collect();
            $totalHours = [];
            $dateofbirth = date('Y-m-d');
            
            return view('storeowner.roster.searchprintroster', compact('weeknumber', 'year', 'rostersByEmployee', 'employees', 'totalHours', 'dateofbirth'));
        }
        
        // Handle POST request (process search)
        $validated = $request->validate([
            'dateofbirth' => 'required|date',
        ]);
        
        $dateofbirth = $validated['dateofbirth'];
        
        $storeid = $this->getStoreId();
        
        $date = new \DateTime($dateofbirth);
        $weeknumber = (int) $date->format('W');
        $year = (int) $date->format('Y');
        
        $yearModel = Year::where('year', $year)->first();
        if ($yearModel) {
            $week = Week::where('weeknumber', $weeknumber)
                ->where('yearid', $yearModel->yearid)
                ->first();
        } else {
            $week = null;
        }
        
        $weekRosters = collect();
        $employees = collect();
        $totalHours = [];
        
        if ($week) {
            $weekRosters = WeekRoster::where('storeid', $storeid)
                ->where('weekid', $week->weekid)
                ->with(['employee' => function($q) {
                    $q->where('status', '!=', 'Deactivate');
                }])
                ->whereHas('employee', function($q) {
                    $q->where('status', '!=', 'Deactivate');
                })
                ->get();
            
            $employeeIds = $weekRosters->pluck('employeeid')->unique();
            $employees = StoreEmployee::whereIn('employeeid', $employeeIds)
                ->where('status', 'Active')
                ->get();
            
            // Calculate total hours per employee from roster entries
            foreach ($employeeIds as $empId) {
                $employeeRosters = $weekRosters->where('employeeid', $empId);
                $total = 0;
                foreach ($employeeRosters as $roster) {
                    if ($roster->start_time != '00:00:00' && $roster->end_time != '00:00:00') {
                        $start = strtotime($roster->start_time);
                        $end = strtotime($roster->end_time);
                        $diff = ($end - $start) / 3600; // Convert seconds to hours
                        $total += $diff;
                    }
                }
                $totalHours[$empId] = round($total, 2);
            }
        }
        
        $rostersByEmployee = $weekRosters->groupBy('employeeid');
        
        return view('storeowner.roster.searchprintroster', compact('weeknumber', 'year', 'rostersByEmployee', 'employees', 'totalHours', 'dateofbirth'));
    }

    /**
     * Search weekly roster by week.
     */
    public function searchweekroster(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        // Check for POST data first (from search form), then session (for redirects after save), then query param (for redirects), then default to today
        $dateInput = $request->input('dateofbirth') ?? session('roster_search_date') ?? $request->query('dateofbirth');
        
        // Clear session date after using it
        if (session('roster_search_date')) {
            session()->forget('roster_search_date');
        }
        
        // If no date provided (GET request or no POST data), show the search form with today's date
        if (!$dateInput) {
            $dateInput = date('Y-m-d'); // Use current date format for HTML5 input
            $week = null;
            $weeknumber = null;
            $year = date('Y');
            $weekRosters = collect();
            $rostersByEmployee = collect();
            $totalHours = [];
            
            return view('storeowner.roster.rosterforweek', compact('week', 'rostersByEmployee', 'weeknumber', 'year', 'totalHours', 'dateInput'));
        }
        
        try {
            // Try yyyy-mm-dd format first (HTML5 date input)
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateInput)) {
                $date = new \DateTime($dateInput);
            } else {
                $date = \DateTime::createFromFormat('d-m-Y', $dateInput);
                if (!$date) {
                    throw new \Exception('Invalid date format');
                }
            }
        } catch (\Exception $e) {
            return redirect()->route('storeowner.roster.searchweekroster')
                ->with('error', 'Invalid date format. Please use dd-mm-yyyy or yyyy-mm-dd format.');
        }
        
        $storeid = $this->getStoreId();

        $weeknumber = (int) $date->format('W');
        $year = (int) $date->format('Y');
        
        // Get or create year
        $yearModel = Year::where('year', $year)->first();
        if (!$yearModel) {
            // Create year if it doesn't exist
            $yearModel = Year::create(['year' => $year]);
        }
        
        // Get week by weeknumber and yearid
        $weekResult = Week::where('weeknumber', $weeknumber)
            ->where('yearid', $yearModel->yearid)
            ->first();
        
        $weekid = $weekResult ? $weekResult->weekid : null;
        $week = $weekResult;
        
        $weekRosters = collect();
        $rostersByEmployee = collect();
        $employees = [];
        $totalHours = [];
        
        if ($weekid) {
            // Get week rosters for this week
            $weekRosters = WeekRoster::where('storeid', $storeid)
                ->where('weekid', $weekid)
                ->with(['employee' => function($q) {
                    $q->where('status', '!=', 'Deactivate');
                }])
                ->whereHas('employee', function($q) {
                    $q->where('status', '!=', 'Deactivate');
                })
                ->get();
            
            // Get employees who already have rosters (for display table)
            $employeeIds = $weekRosters->pluck('employeeid')->unique();
            foreach ($employeeIds as $empId) {
                $employee = StoreEmployee::where('employeeid', $empId)
                    ->where('status', '!=', 'Deactivate')
                    ->first();
                if ($employee) {
                    $employees[] = $employee;
                }
            }
            
            // Group by employee
            $rostersByEmployee = $weekRosters->groupBy('employeeid');
            
            // Calculate total hours per employee
            foreach ($employees as $employee) {
                $empId = $employee->employeeid;
                $employeeRosters = $weekRosters->where('employeeid', $empId);
                $total = 0;
                foreach ($employeeRosters as $roster) {
                    if ($roster->start_time != '00:00:00' && $roster->end_time != '00:00:00') {
                        $start = strtotime($roster->start_time);
                        $end = strtotime($roster->end_time);
                        $diff = ($end - $start) / 3600; // Convert seconds to hours
                        $total += ceil($diff); // Use ceil like CI line 138
                    }
                }
                $totalHours[$empId] = $total;
            }
        }
        
        return view('storeowner.roster.rosterforweek', compact('week', 'weekid', 'rostersByEmployee', 'employees', 'weeknumber', 'year', 'totalHours', 'dateInput', 'weekRosters'));
    }

    /**
     * Add or edit weekly roster (from Search & Edit form).
     */
    public function addeditweekroster(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'hdnweekid' => 'required|integer',
            'employeeid' => 'required|string',
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
        
        $storeid = $this->getStoreId();
        
        // Parse employeeid - can be either direct ID or "employeeid||departmentid" format
        $employeeid = $validated['employeeid'];
        if (strpos($employeeid, '||') !== false) {
            $employeeParts = explode('||', $employeeid);
            $employeeid = $employeeParts[0];
        }
        $weekid = $validated['hdnweekid'];
        
        // Get employee data
        $employee = StoreEmployee::where('employeeid', $employeeid)->first();
        if (!$employee) {
            return redirect()->back()
                ->with('error', 'Employee not found.');
        }
        
        // Check if roster already exists for this employee/week and delete it (like CI line 1048-1050)
        $existingRosters = WeekRoster::where('storeid', $storeid)
            ->where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->get();
        
        if ($existingRosters->count() > 0) {
            WeekRoster::where('storeid', $storeid)
                ->where('employeeid', $employeeid)
                ->where('weekid', $weekid)
                ->delete();
        }
        
        // Get week information to calculate dates
        $week = \App\Models\Week::find($weekid);
        if (!$week) {
            return redirect()->back()
                ->with('error', 'Week not found.');
        }
        
        $yearModel = \App\Models\Year::find($week->yearid);
        if (!$yearModel) {
            return redirect()->back()
                ->with('error', 'Year not found.');
        }
        
        $weeknumber = $week->weeknumber;
        $year = $yearModel->year;
        
        // Calculate dates for each day of the week (ISO week starts on Monday)
        // Get the Monday of the ISO week
        $monday = new \DateTime();
        $monday->setISODate($year, $weeknumber, 1); // ISO week: 1 = Monday
        
        // Calculate dates for each day
        $dayDates = [
            'Monday' => $monday->format('Y-m-d'),
            'Tuesday' => (clone $monday)->modify('+1 day')->format('Y-m-d'),
            'Wednesday' => (clone $monday)->modify('+2 days')->format('Y-m-d'),
            'Thursday' => (clone $monday)->modify('+3 days')->format('Y-m-d'),
            'Friday' => (clone $monday)->modify('+4 days')->format('Y-m-d'),
            'Saturday' => (clone $monday)->modify('+5 days')->format('Y-m-d'),
            'Sunday' => (clone $monday)->modify('+6 days')->format('Y-m-d'),
        ];
        
        // Create rosters for each day (like CI lines 1056-1290)
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        
        foreach ($days as $day) {
            $startKey = $day . '_start';
            $endKey = $day . '_end';
            $startTime = $validated[$startKey];
            $endTime = $validated[$endKey];
            
            $workStatus = ($startTime === 'off' || $endTime === 'off') ? 'off' : 'on';
            
            WeekRoster::create([
                'storeid' => $storeid,
                'employeeid' => $employeeid,
                'departmentid' => $employee->departmentid,
                'weekid' => $weekid,
                'day' => $day,
                'day_date' => $dayDates[$day],
                'start_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($startTime)),
                'end_time' => $workStatus === 'off' ? '00:00:00' : date('H:i:s', strtotime($endTime)),
                'break_every_hrs' => $employee->break_every_hrs ?? 0,
                'break_min' => $employee->break_min ?? 0,
                'paid_break' => $employee->paid_break ?? 'Yes',
                'work_status' => $workStatus,
                'insertdatetime' => now(),
                'insertip' => $request->ip(),
            ]);
        }
        
        // Get the date from request to preserve the week that was being edited
        // Client wants to stay on the same week after editing
        $dateInput = $request->input('dateofbirth');
        
        if ($dateInput) {
            // Store in session to pass to the redirect (since redirect uses GET, we'll use session)
            session()->flash('roster_search_date', $dateInput);
            
            // Redirect to searchweekroster with the date parameter to stay on the same week
            return redirect()->route('storeowner.roster.searchweekroster', ['dateofbirth' => $dateInput])
                ->with('success', 'Roster Edited Successfully.');
        }
        
        // Fallback to current week if no date provided
        return redirect()->route('storeowner.roster.searchweekroster')
            ->with('success', 'Roster Edited Successfully.');
    }

    /**
     * Email roster to all employees.
     */
    public function emailRoster(): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $storeid = $this->getStoreId();
        
        $date = date('Y-m-d');
        $weeknumber = (int) date('W');
        $year = (int) date('Y');
        
        $yearModel = Year::where('year', $year)->first();
        if ($yearModel) {
            $week = Week::where('weeknumber', $weeknumber)
                ->where('yearid', $yearModel->yearid)
                ->first();
        } else {
            $week = null;
        }
        
        if (!$week) {
            return redirect()->route('storeowner.roster.weekroster')
                ->with('error', 'No roster found for current week.');
        }
        
        $weekRosters = WeekRoster::where('storeid', $storeid)
            ->where('weekid', $week->weekid)
            ->with(['employee' => function($q) {
                $q->where('status', '!=', 'Deactivate');
            }])
            ->whereHas('employee', function($q) {
                $q->where('status', '!=', 'Deactivate');
            })
            ->get();
        
        // Group rosters by employee
        $rostersByEmployee = $weekRosters->groupBy('employeeid');
        
        // Get store info
        $store = \App\Models\Store::find($storeid);
        $siteName = $store->storename ?? config('app.name');
        $siteEmail = $store->store_email ?? config('mail.from.address');
        
        // Send email to each employee
        foreach ($rostersByEmployee as $employeeId => $rosters) {
            $employee = StoreEmployee::find($employeeId);
            
            if (!$employee || !$employee->emailid) {
                continue;
            }
            
            try {
                \Illuminate\Support\Facades\Mail::send('storeowner.roster.email_roster', [
                    'employee' => $employee,
                    'my_roster' => $rosters,
                    'weeknumber' => $weeknumber,
                    'year' => $year,
                    'sitename' => $siteName,
                ], function($message) use ($employee, $siteEmail, $siteName) {
                    $message->to($employee->emailid, $employee->firstname . ' ' . $employee->lastname)
                        ->subject('Your Weekly Roster - ' . $siteName);
                });
            } catch (\Exception $e) {
                // Log error but continue sending to other employees
                \Log::error('Failed to send roster email to employee ' . $employeeId . ': ' . $e->getMessage());
            }
        }
        
        return redirect()->route('storeowner.roster.weekroster')
            ->with('success', 'Roster emailed to all employees successfully.');
    }
}

