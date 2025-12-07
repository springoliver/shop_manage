<?php

namespace App\Http\StoreOwner\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\StoreEmployee;
use App\Models\Week;
use App\Models\Year;
use App\Models\EmpPayrollHrs;
use App\Services\StoreOwner\ModuleService;
use App\Services\StoreOwner\RosterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class EmployeePayrollController extends Controller
{
    protected ModuleService $moduleService;
    protected RosterService $rosterService;

    public function __construct(ModuleService $moduleService, RosterService $rosterService)
    {
        $this->moduleService = $moduleService;
        $this->rosterService = $rosterService;
    }

    /**
     * Check if Employee Payroll module is installed.
     */
    protected function checkModuleAccess()
    {
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if (!$this->moduleService->isModuleInstalled($storeid, 'Employee Payroll')) {
            return redirect()->route('storeowner.modulesetting.index')
                ->with('error', 'Please Buy Module to Activate');
        }
        
        return null;
    }

    /**
     * Display a listing of employee payslips (grouped by employee).
     */
    public function index(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $payslips = DB::table('stoma_payslip as p')
            ->select(
                DB::raw('MAX(p.payslipid) as payslipid'),
                'p.storeid',
                'e.employeeid',
                DB::raw('MAX(p.payslipname) as payslipname'),
                DB::raw('MAX(p.weekid) as weekid'),
                DB::raw('MAX(p.year) as year'),
                DB::raw('MAX(p.insertdatetime) as insertdatetime'),
                DB::raw('MAX(p.insertip) as insertip'),
                DB::raw('MAX(p.editdatetime) as editdatetime'),
                DB::raw('MAX(p.editip) as editip'),
                DB::raw('MAX(s.store_name) as store_name'),
                DB::raw('MAX(e.firstname) as firstname'),
                DB::raw('MAX(e.lastname) as lastname'),
                DB::raw('MAX(e.usergroupid) as usergroupid'),
                DB::raw('MAX(e.emailid) as emailid'),
                DB::raw('MAX(u.groupname) as groupname'),
                DB::raw('MAX(w.weeknumber) as weeknumber')
            )
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->where('e.status', 'Active')
            ->where('p.storeid', $storeid)
            ->groupBy('p.storeid', 'e.employeeid')
            ->orderBy(DB::raw('MAX(p.payslipid)'), 'DESC')
            ->get();
        
        return view('storeowner.employeepayroll.index', compact('payslips'));
    }

    /**
     * Show payslips for a specific employee.
     */
    public function payslipsByEmployee($employeeid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $employeeid = base64_decode($employeeid);
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $payslips = DB::table('stoma_payslip as p')
            ->select('p.*', 's.store_name', 'e.firstname', 'e.lastname', 'e.usergroupid', 'e.emailid', 'u.groupname', 'w.weeknumber')
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->where('p.employeeid', $employeeid)
            ->where('p.storeid', $storeid)
            ->orderBy('p.weekid', 'DESC')
            ->orderBy('p.year', 'DESC')
            ->get();
        
        return view('storeowner.employeepayroll.payslipsby_employee', compact('payslips'));
    }

    /**
     * Show the form for adding a payslip.
     */
    public function addPayslip(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->select('firstname', 'lastname', 'employeeid')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        // Get all payslips for listing
        $allPayslips = DB::table('stoma_payslip as p')
            ->select('p.*', 's.store_name', 'e.firstname', 'e.lastname', 'e.usergroupid', 'e.emailid', 'u.groupname', 'w.weeknumber')
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->where('e.status', 'Active')
            ->where('p.storeid', $storeid)
            ->orderBy('p.payslipid', 'DESC')
            ->get();
        
        return view('storeowner.employeepayroll.addpayslip', compact('employees', 'allPayslips'));
    }

    /**
     * Store a newly uploaded payslip.
     */
    public function storePayslip(Request $request): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $validated = $request->validate([
            'employeeid' => 'required|integer|exists:stoma_employee,employeeid',
            'myweek_num' => 'required|integer',
            'my_year' => 'required|string|size:4',
            'doc' => 'required|file|mimes:pdf|max:51200', // 50MB
        ]);
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        if ($request->hasFile('doc')) {
            $file = $request->file('doc');
            $payslipname = 'Week-' . $validated['myweek_num'] . '-' . $storeid . $validated['employeeid'] . $validated['my_year'] . '.pdf';
            
            // Get or create week
            $yearModel = Year::firstOrCreate(['year' => $validated['my_year']]);
            $weekModel = Week::firstOrCreate(
                [
                    'weeknumber' => $validated['myweek_num'],
                    'yearid' => $yearModel->yearid,
                ]
            );
            
            // Check if payslip already exists
            $existingPayslip = Payslip::where('payslipname', $payslipname)->first();
            
            if ($existingPayslip) {
                // Delete old file
                if (Storage::disk('public')->exists('payslips/' . $payslipname)) {
                    Storage::disk('public')->delete('payslips/' . $payslipname);
                }
                
                // Store new file
                $filePath = $file->storeAs('payslips', $payslipname, 'public');
                
                $existingPayslip->editdatetime = now();
                $existingPayslip->editip = $request->ip();
                $existingPayslip->save();
                
                return redirect()->route('storeowner.employeepayroll.index')
                    ->with('success', 'Payslip successfully updated for the employee for week ' . $validated['myweek_num']);
            } else {
                // Store new file
                $filePath = $file->storeAs('payslips', $payslipname, 'public');
                
                $payslip = new Payslip();
                $payslip->storeid = $storeid;
                $payslip->employeeid = $validated['employeeid'];
                $payslip->payslipname = $payslipname;
                $payslip->weekid = $weekModel->weekid;
                $payslip->year = $validated['my_year'];
                $payslip->insertdatetime = now();
                $payslip->insertip = $request->ip();
                $payslip->save();
                
                return redirect()->route('storeowner.employeepayroll.index')
                    ->with('success', 'Payslip Added Successfully.');
            }
        }
        
        return redirect()->back()
            ->with('error', 'Something went wrong. Please try again.')
            ->withInput();
    }

    /**
     * Display the specified payslip.
     */
    public function view($payslipid): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $payslipid = base64_decode($payslipid);
        
        $payslip = DB::table('stoma_payslip as p')
            ->select('p.*', 'e.firstname', 'e.lastname', 'e.usergroupid', 'e.emailid', 's.store_name', 'w.weeknumber', 'u.groupname')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->where('p.payslipid', $payslipid)
            ->first();
        
        if (!$payslip) {
            return redirect()->route('storeowner.employeepayroll.index')
                ->with('error', 'Payslip not found.');
        }
        
        // Get payroll details for this employee/week
        $payrollDetails = DB::table('stoma_emp_payroll')
            ->where('storeid', $payslip->storeid)
            ->where('employeeid', $payslip->employeeid)
            ->where('weekid', $payslip->weekid)
            ->get();
        
        return view('storeowner.employeepayroll.view', compact('payslip', 'payrollDetails'));
    }

    /**
     * Download payslip PDF.
     */
    public function downloadPdf($id)
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $payslipid = base64_decode($id);
        $payslip = Payslip::findOrFail($payslipid);
        
        $filePath = storage_path('app/public/payslips/' . $payslip->payslipname);
        
        if (file_exists($filePath)) {
            return response()->download($filePath, $payslip->payslipname);
        }
        
        return redirect()->route('storeowner.employeepayroll.index')
            ->with('error', 'Payslip file not found.');
    }

    /**
     * Remove the specified payslip.
     */
    public function destroy($payslipid): RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $payslipid = base64_decode($payslipid);
        $payslip = Payslip::findOrFail($payslipid);
        
        // Delete file
        if (Storage::disk('public')->exists('payslips/' . $payslip->payslipname)) {
            Storage::disk('public')->delete('payslips/' . $payslip->payslipname);
        }
        
        $payslip->delete();
        
        return redirect()->route('storeowner.employeepayroll.index')
            ->with('success', 'Payslip deleted successfully.');
    }

    /**
     * Process payroll view (shows payroll hours).
     */
    public function processPayroll(): View|\Illuminate\Http\RedirectResponse
    {
        $moduleCheck = $this->checkModuleAccess();
        if ($moduleCheck) {
            return $moduleCheck;
        }
        
        $user = Auth::guard('storeowner')->user();
        $storeid = session('storeid', $user->stores->first()->storeid ?? 0);
        
        // Get all payroll hours grouped by employee, week, and year
        $payrollHrs = DB::table('stoma_emp_payroll_hrs as ep')
            ->select(
                DB::raw('MIN(ep.payroll_id) as payroll_id'),
                'ep.storeid',
                'ep.employeeid',
                'ep.weekno',
                DB::raw('MIN(ep.week_start) as week_start'),
                DB::raw('MIN(ep.week_end) as week_end'),
                'ep.year',
                DB::raw('SUM(CAST(ep.hours_worked AS DECIMAL(10,2))) AS hours_worked'),
                DB::raw('MAX(ep.numberofdaysworked) as numberofdaysworked'),
                DB::raw('MAX(ep.break_deducted) as break_deducted'),
                DB::raw('MAX(ep.sunday_hrs) as sunday_hrs'),
                DB::raw('MAX(ep.owertime1_hrs) as owertime1_hrs'),
                DB::raw('MAX(ep.owertime2_hrs) as owertime2_hrs'),
                DB::raw('MAX(ep.holiday_hrs) as holiday_hrs'),
                DB::raw('MAX(ep.holiday_days) as holiday_days'),
                DB::raw('MAX(ep.sickpay_hrs) as sickpay_hrs'),
                DB::raw('MAX(ep.extras1_hrs) as extras1_hrs'),
                DB::raw('MAX(ep.extras2_hrs) as extras2_hrs'),
                DB::raw('MAX(ep.total_hours) as total_hours'),
                DB::raw('MAX(ep.notes) as notes'),
                DB::raw('MAX(ep.insertdate) as insertdate'),
                DB::raw('MAX(ep.insertip) as insertip'),
                DB::raw('MAX(ep.editdate) as editdate'),
                DB::raw('MAX(ep.editip) as editip')
            )
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'ep.employeeid')
            ->where('ep.storeid', $storeid)
            ->groupBy('ep.employeeid', 'ep.weekno', 'ep.year', 'ep.storeid')
            ->orderBy(DB::raw('MIN(ep.week_start)'), 'DESC')
            ->get();
        
        $employees = StoreEmployee::where('storeid', $storeid)
            ->where('status', 'Active')
            ->select('firstname', 'lastname', 'employeeid')
            ->orderBy('firstname', 'ASC')
            ->get();
        
        return view('storeowner.employeepayroll.process_payroll', compact('payrollHrs', 'employees'));
    }

    /**
     * Get week details from date (AJAX).
     */
    public function getWeekDetails(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
        ]);
        
        $date = Carbon::parse($validated['date']);
        $year = $date->year;
        $weekNumber = $date->week;
        
        // Get week start and end dates
        $weekStart = $date->copy()->startOfWeek();
        $weekEnd = $date->copy()->endOfWeek();
        
        return response()->json([
            'week_num' => $weekNumber,
            'year' => (string)$year,
            'week_start' => $weekStart->format('Y-m-d'),
            'week_end' => $weekEnd->format('Y-m-d'),
        ]);
    }
}

