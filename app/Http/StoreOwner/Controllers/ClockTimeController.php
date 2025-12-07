<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StoreEmployee;
use App\Services\StoreOwner\ModuleService;
use App\Services\StoreOwner\ClockTimeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class ClockTimeController extends Controller
{
    protected ModuleService $moduleService;
    protected ClockTimeService $clockTimeService;

    public function __construct(ModuleService $moduleService, ClockTimeService $clockTimeService)
    {
        $this->moduleService = $moduleService;
        $this->clockTimeService = $clockTimeService;
    }

    /**
     * Check if Clock in-out module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Clock in-out')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of clock in-out records.
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $clockDetails = $this->clockTimeService->getClockDetails($storeid);
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        $searchDate = Carbon::now()->format('d-m-Y');
        $searchDateEnd = Carbon::now()->format('d-m-Y');
        
        return view('storeowner.clocktime.index', compact('clockDetails', 'employees', 'searchDate', 'searchDateEnd'));
    }

    /**
     * Handle search/report request.
     */
    public function clockReport(Request $request): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'date' => 'required',
            'date_end' => 'required',
            'employeeid' => 'nullable|array',
            'employeeid.*' => 'nullable|integer',
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Parse dd-mm-yyyy format
        $date = Carbon::createFromFormat('d-m-Y', $validated['date'])->format('Y-m-d');
        $endDate = Carbon::createFromFormat('d-m-Y', $validated['date_end'])->format('Y-m-d');
        
        $employeeids = [];
        if (!empty($validated['employeeid'])) {
            $employeeids = array_filter($validated['employeeid']);
        }
        
        // If no employees selected, get all employees
        if (empty($employeeids)) {
            $employees = StoreEmployee::where('storeid', $storeid)
                ->where('status', '!=', 'Deactivate')
                ->pluck('employeeid')
                ->toArray();
            $employeeids = $employees;
        }
        
        $clockDetails = $this->clockTimeService->getClockDetailsByDate($storeid, $date, $endDate, $employeeids);
        
        // Add roster times and calculate totals
        foreach ($clockDetails as $detail) {
            $rosterData = $this->clockTimeService->getRosterHour($detail->employeeid, $detail->weekid, $detail->day);
            $detail->roster_start_time = $rosterData['start_time'];
            $detail->roster_end_time = $rosterData['end_time'];
            
            // Calculate total roster hours
            $rosterStart = Carbon::parse($detail->roster_start_time);
            $rosterEnd = Carbon::parse($detail->roster_end_time);
            $detail->total_roster_minutes = $rosterEnd->diffInMinutes($rosterStart);
            
            // Calculate clock in-out hours
            if ($detail->status !== 'clockout' && $detail->clockin && $detail->clockout) {
                $clockin = Carbon::parse($detail->clockin);
                $clockout = Carbon::parse($detail->clockout);
                $detail->timediff = $clockout->diffInMinutes($clockin);
            } else {
                $detail->timediff = null;
            }
        }
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', '!=', 'Deactivate')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        $searchDate = Carbon::parse($validated['date'])->format('d-m-Y');
        $searchDateEnd = Carbon::parse($validated['date_end'])->format('d-m-Y');
        
        return view('storeowner.clocktime.index', compact('clockDetails', 'employees', 'searchDate', 'searchDateEnd', 'employeeids'));
    }

    /**
     * Export clock in-out data to PDF.
     */
    public function exportPdf(Request $request)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'date' => 'required',
            'date_end' => 'required',
            'employeeid' => 'nullable|array',
            'employeeid.*' => 'nullable|integer',
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Parse dd-mm-yyyy format
        try {
            $date = Carbon::createFromFormat('d-m-Y', $validated['date'])->format('Y-m-d');
            $endDate = Carbon::createFromFormat('d-m-Y', $validated['date_end'])->format('Y-m-d');
        } catch (\Exception $e) {
            return redirect()->route('storeowner.clocktime.index')
                ->with('error', 'Invalid date format. Please use dd-mm-yyyy format.');
        }
        
        $employeeids = [];
        if (!empty($validated['employeeid'])) {
            $employeeids = array_filter($validated['employeeid']);
        }
        
        // If no employees selected, get all employees
        if (empty($employeeids)) {
            $employees = StoreEmployee::where('storeid', $storeid)
                ->where('status', '!=', 'Deactivate')
                ->pluck('employeeid')
                ->toArray();
            $employeeids = $employees;
        }
        
        $clockDetails = $this->clockTimeService->getClockDetailsByDate($storeid, $date, $endDate, $employeeids);
        
        // Add roster times and calculate totals
        foreach ($clockDetails as $detail) {
            $rosterData = $this->clockTimeService->getRosterHour($detail->employeeid, $detail->weekid, $detail->day);
            $detail->roster_start_time = $rosterData['start_time'];
            $detail->roster_end_time = $rosterData['end_time'];
            
            // Calculate total roster hours
            $rosterStart = Carbon::parse($detail->roster_start_time);
            $rosterEnd = Carbon::parse($detail->roster_end_time);
            $detail->total_roster_minutes = $rosterEnd->diffInMinutes($rosterStart);
            
            // Calculate clock in-out hours
            if ($detail->status !== 'clockout' && $detail->clockin && $detail->clockout) {
                $clockin = Carbon::parse($detail->clockin);
                $clockout = Carbon::parse($detail->clockout);
                $detail->timediff = $clockout->diffInMinutes($clockin);
            } else {
                $detail->timediff = null;
            }
        }
        
        // For now, return a simple response indicating PDF export (will need PDF library)
        // TODO: Implement PDF generation using Laravel PDF library (e.g., barryvdh/laravel-dompdf)
        return redirect()->route('storeowner.clocktime.index')
            ->with('info', 'PDF export functionality will be implemented. For now, please use the search results.');
    }

    /**
     * Display employee holidays page.
     */
    public function employeeHolidays(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $empPayrollHrs = $this->clockTimeService->getEmployeeHolidayCalculation($storeid);
        
        return view('storeowner.clocktime.employee_holidays', compact('empPayrollHrs'));
    }

    /**
     * Display compare weekly hours page (Employee Hours tab).
     */
    public function compareWeeklyHrs(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $empPayrollHrs = $this->clockTimeService->getAllWeekHrs($storeid);
        
        return view('storeowner.clocktime.compare_weekly_hrs', compact('empPayrollHrs'));
    }

    /**
     * Display all employees weekly hours page (Weekly Hours tab).
     */
    public function allemployeeWeeklyhrs(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $empPayrollHrs = $this->clockTimeService->getAllEmployeesWeekHrs($storeid);
        
        return view('storeowner.clocktime.allemployee_weeklyhrs', compact('empPayrollHrs'));
    }

    /**
     * Display monthly hours for all employees page (Monthly Hours tab).
     */
    public function monthlyHrsAllEmployee(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $empPayrollHrs = $this->clockTimeService->getMonthlyHrsAllEmployee($storeid);
        
        return view('storeowner.clocktime.monthly_hrs_allemployee', compact('empPayrollHrs'));
    }
}

