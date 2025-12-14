<?php

namespace App\Http\Employee\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Payslip;
use App\Models\EmpPayroll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PayrollController extends Controller
{
    /**
     * Display a listing of the payrolls for the authenticated employee.
     */
    public function index(): View
    {
        $employee = Auth::guard('employee')->user();
        
        // Get all payslips for this employee
        $myPayroll = DB::table('stoma_payslip as p')
            ->select(
                'p.payslipid',
                'p.storeid',
                'p.employeeid',
                'p.payslipname',
                'p.weekid',
                's.store_name',
                'e.firstname',
                'e.lastname',
                'e.usergroupid',
                'e.emailid',
                'u.groupname',
                'w.weeknumber'
            )
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->where('p.storeid', $employee->storeid)
            ->where('p.employeeid', $employee->employeeid)
            ->orderBy('p.payslipid', 'DESC')
            ->get()
            ->map(function ($payroll) {
                return [
                    'payslipid' => $payroll->payslipid,
                    'storeid' => $payroll->storeid,
                    'employeeid' => $payroll->employeeid,
                    'weekid' => $payroll->weekid,
                    'firstname' => $payroll->firstname ?? '',
                    'lastname' => $payroll->lastname ?? '',
                    'store_name' => $payroll->store_name ?? '',
                    'payslipname' => $payroll->payslipname ?? '',
                    'weeknumber' => $payroll->weeknumber ?? '',
                ];
            });
        
        return view('employee.payroll.index', compact('myPayroll'));
    }

    /**
     * Display the specified payroll.
     */
    public function show(string $storeid, string $employeeid, string $weekid): View|RedirectResponse
    {
        $storeid = base64_decode($storeid);
        $employeeid = base64_decode($employeeid);
        $weekid = base64_decode($weekid);
        
        $employee = Auth::guard('employee')->user();
        
        // Ensure the employee can only view their own payroll
        if ($employee->employeeid != $employeeid) {
            abort(403);
        }
        
        // Get payslip information
        $myPayroll = DB::table('stoma_payslip as p')
            ->select(
                'p.payslipid',
                'p.storeid',
                'p.employeeid',
                'p.payslipname',
                'p.weekid',
                's.store_name',
                'e.firstname',
                'e.lastname',
                'e.usergroupid',
                'e.emailid',
                'u.groupname',
                'w.weeknumber'
            )
            ->leftJoin('stoma_store as s', 's.storeid', '=', 'p.storeid')
            ->leftJoin('stoma_employee as e', 'e.employeeid', '=', 'p.employeeid')
            ->leftJoin('stoma_usergroup as u', 'u.usergroupid', '=', 'e.usergroupid')
            ->leftJoin('stoma_week as w', 'w.weekid', '=', 'p.weekid')
            ->where('p.storeid', $storeid)
            ->where('p.employeeid', $employeeid)
            ->where('p.weekid', $weekid)
            ->first();
        
        if (!$myPayroll) {
            abort(404);
        }
        
        // Convert to array for easier access in view
        $myPayroll = (array) $myPayroll;
        
        // Get payroll details (daily breakdown)
        $payroll = EmpPayroll::where('storeid', $storeid)
            ->where('employeeid', $employeeid)
            ->where('weekid', $weekid)
            ->orderByRaw("FIELD(weekday, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->get();
        
        return view('employee.payroll.view', compact('myPayroll', 'payroll'));
    }

    /**
     * Download payslip PDF.
     */
    public function downloadPdf(string $payslipid): StreamedResponse|RedirectResponse
    {
        $payslipid = base64_decode($payslipid);
        $employee = Auth::guard('employee')->user();
        
        $payslip = Payslip::where('payslipid', $payslipid)
            ->where('employeeid', $employee->employeeid)
            ->firstOrFail();
        
        // Get payslip file path
        $payslipPath = 'payslips/' . $payslip->payslipname;
        
        // Check if file exists in storage
        if (Storage::disk('public')->exists($payslipPath)) {
            return Storage::disk('public')->download($payslipPath, $payslip->payslipname);
        }
        
        // If not in storage, check in public path (CI compatibility)
        $publicPath = public_path('payslips/' . $payslip->payslipname);
        if (file_exists($publicPath)) {
            return response()->download($publicPath, $payslip->payslipname);
        }
        
        return redirect()->route('employee.payroll.index')
            ->with('error', 'Payslip file not found.');
    }
}

